<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(Request $request)
    {
        // Valider les données d'entrée
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Envoyer un email de vérification (cette partie nécessiterait une implémentation supplémentaire)
        // $this->sendVerificationEmail($user);

        // Générer un token pour l'utilisateur
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Inscription réussie. Veuillez vérifier votre email pour activer votre compte.'
        ], 201);
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login(Request $request)
    {
        // Valider les données d'entrée
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tentative de connexion
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Les identifiants fournis sont incorrects.'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Supprimer les anciens tokens
        $user->tokens()->delete();
        
        // Créer un nouveau token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Connexion réussie.'
        ]);
    }

    /**
     * Déconnexion d'un utilisateur
     */
    public function logout(Request $request)
    {
        // Supprimer le token actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ]);
    }

    /**
     * Récupérer les informations de l'utilisateur connecté
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    /**
     * Rediriger vers le fournisseur OAuth
     */
    public function redirectToProvider($provider)
    {
        // Vérifier que le provider est supporté
        if (!in_array($provider, ['google', 'facebook', 'apple'])) {
            return response()->json([
                'message' => 'Fournisseur non supporté.'
            ], 400);
        }

        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl()
        ]);
    }

    /**
     * Gérer le callback du fournisseur OAuth
     */
    public function handleProviderCallback($provider, Request $request)
    {
        try {
            // Vérifier que le provider est supporté
            if (!in_array($provider, ['google', 'facebook', 'apple'])) {
                return response()->json([
                    'message' => 'Fournisseur non supporté.'
                ], 400);
            }

            // Récupérer l'utilisateur depuis le fournisseur OAuth
            $socialUser = Socialite::driver($provider)->stateless()->user();
            
            // Rechercher l'utilisateur dans notre base de données
            $user = User::where('email', $socialUser->getEmail())->first();
            
            // Si l'utilisateur n'existe pas, le créer
            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'social_id' => $socialUser->getId(),
                    'social_type' => $provider,
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified' => true,
                ]);
            } 
            // Si l'utilisateur existe mais n'a pas de social_id, mettre à jour les informations sociales
            else if (empty($user->social_id)) {
                $user->update([
                    'social_id' => $socialUser->getId(),
                    'social_type' => $provider,
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified' => true,
                ]);
            }
            
            // Supprimer les anciens tokens
            $user->tokens()->delete();
            
            // Générer un nouveau token
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'Connexion réussie via ' . ucfirst($provider)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la connexion sociale.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour le profil de l'utilisateur
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'required_with:password|current_password',
            'password' => [
                'required_with:current_password',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Mise à jour des champs fournis
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('email') && $user->email !== $request->email) {
            $user->email = $request->email;
            $user->email_verified = false;
            // Renvoyer un email de vérification ici si nécessaire
        }
        
        if ($request->has('password') && $request->has('current_password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return response()->json([
            'user' => $user,
            'message' => 'Profil mis à jour avec succès.'
        ]);
    }
}
