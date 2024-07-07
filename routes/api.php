<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

Route::post('/process-data', function (Request $request) {
    Log::info('Requête reçue pour traiter les données.');

    $data = $request->input('data');
    $file = $request->file('file');
    
    if ($file) {
        // Générer un nouveau nom de fichier
        $fileName = $file->getClientOriginalName();
        Log::info("Nom du fichier téléchargé : $fileName");

        // Enregistrer le fichier dans le disque 'public'
        $file->storeAs('uploads', $fileName, 'public');
        Log::info("Fichier enregistré à : uploads/$fileName");

        // Appeler le script Python pour traiter le fichier
        $inputPath = storage_path('app\\public\\uploads\\' . $fileName);
        $outputPath = storage_path('app\\public\\processed_\\testestets.png');
        Log::info("Chemin d'entrée : $inputPath");
        Log::info("Chemin de sortie : $outputPath");

        // Chemin direct vers l'exécutable Python (ajustez ce chemin selon votre installation)
        $pythonPath = 'C:\Users\enzof\AppData\Local\Programs\Python\Python312\python.exe';

        // Mettre à jour le chemin vers le script Python
        $process = new Process([$pythonPath, base_path('scripts/remove_background.py'), $inputPath, $outputPath]);
        $process->run();

        // Journaliser les sorties et les erreurs du processus
        if (!$process->isSuccessful()) {
            Log::error('Le script Python a échoué.');
            Log::error($process->getErrorOutput());
            throw new ProcessFailedException($process);
        }

        Log::info('Le script Python s\'est exécuté avec succès.');
        Log::info($process->getOutput());

        // Retourner les données traitées et l'URL du fichier
        $processedData = $data + 1;
        return response()->json(['data' => $processedData]);
    }

    Log::info('Aucun fichier téléchargé.');
    $processedData = $data + 1;
    return response()->json(['data' => $processedData]);
});

Route::post('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
