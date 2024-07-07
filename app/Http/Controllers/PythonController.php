<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PythonController extends Controller
{
    public function runScript(Request $request)
    {
        // Valider la requête
        $request->validate([
            'api_key' => 'required|string',
            'input_image' => 'required|file|mimes:jpg,png'
        ]);

        // Sauvegarder le fichier d'entrée en mémoire temporaire
        $inputImage = $request->file('input_image');
        $inputPath = $inputImage->getRealPath();

        // Exécuter le script Python
        $process = new Process(['python3', base_path('remove_background.py'), $request->input('api_key'), $inputPath]);
        $process->run();

        // Vérifier si le script a bien été exécuté
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Récupérer la sortie du script Python
        $output = $process->getOutput();

        // Retourner la réponse avec l'image transformée en base64
        return response()->json([
            'success' => true,
            'output_image' => $output
        ]);
    }
}
