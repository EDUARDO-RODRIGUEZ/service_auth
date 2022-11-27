<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Aws\Rekognition\RekognitionClient;

class UsuarioController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "nombre" => "required",
            "email" => "required|unique:usuarios",
            "password" => "required|min:8",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "ok" => false,
                "message" => "error en la validacion de datos",
                "errors" => $validate->errors()
            ], 400);
        }

        if (!$request->hasFile("perfil")) {
            return response()->json([
                "ok" => false,
                "message" => "error en la validacion de datos",
                "errors" => "falta imagen perfil"
            ], 400);
        }

        $file = $request->file("perfil");
        $name = time() . "" . $file->getClientOriginalName();
        $file->storeAs("public", $name);

        $usuariocreated = Usuario::create([
            "nombre" => $request->input("nombre"),
            "email" => $request->input("email"),
            "password" => Hash::make($request->input("password")),
            "perfil" =>  $name,
            "rol_id" => 2
        ]);

        unset($usuariocreated["password"]);

        return response()->json([
            "ok" => true,
            "message" => "Se creo el usuario correctamente",
            "data" => $usuariocreated
        ], 201);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "email" => "required",
            "password" => "required"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "ok" => false,
                "message" => "Bad Request",
                "errors" => $validate->errors()
            ], 400);
        }

        $user = Usuario::where("email", $request->input("email"))->first();

        if (is_null($user)) {
            return response()->json([
                "ok" => false,
                "message" => "Not Found user",
            ], 404);
        }

        if (!Hash::check($request->input("password"), $user->password)) {
            return response()->json([
                "ok" => false,
                "message" => "Invalid password",
            ], 401);
        }

        unset($user["password"]);

        return response()->json([
            "ok" => true,
            "data" => $user
        ]);
    }

    public function compareFace(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'perfil' => 'required',
            'photo' => 'required|image'
        ]);

        if ($validate->fails()) {
            return response()->json([
                "ok" => false,
                "errors" => $validate->errors()
            ], 400);
        }

        $bytesPerfil = Storage::disk('public')->get($request->input("perfil"));

        $bytesPhoto = file_get_contents($request->file('photo')->getRealPath());

        $client = new RekognitionClient([
            'region'    => env("AWS_DEFAULT_REGION"),
            'version'   => 'latest',
            'credentials' => [
                'key' => env("AWS_ACCESS_KEY_ID"),
                'secret' => env("AWS_SECRET_ACCESS_KEY")
            ]
        ]);


        $results = $client->compareFaces([
            'SimilarityThreshold' => 80,
            'SourceImage' => [
                'Bytes' => $bytesPerfil
            ],
            'TargetImage' => [
                'Bytes' => $bytesPhoto
            ]
        ]);

        if (count($results->get("FaceMatches")) == 0) {
            return response()->json([
                "ok" => true,
                "compare" => false,
            ]);
        }

        return response()->json([
            "ok" => true,
            "compare" => true,
            "face" => $results->get("FaceMatches")
        ]);
    }
}
