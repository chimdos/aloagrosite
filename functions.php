<?php

/**
 * Processa um arquivo de imagem enviado, corta para um quadrado, redimensiona e salva.
 * @param array $file O array do arquivo vindo de $_FILES['nome_do_campo'].
 * @param int $targetWidth A largura final da imagem.
 * @param int $targetHeight A altura final da imagem.
 * @return array Retorna ['success' => true, 'filename' => 'nome_do_arquivo.ext'] ou ['success' => false, 'message' => '...'].
 */
function processAndSaveImage($file, $targetWidth, $targetHeight) {
    $uploadDir = 'arquivos/uploads/produtos/';
    
    if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
        return ['success' => false, 'message' => 'Erro: A pasta de uploads não existe ou não tem permissão de escrita.'];
    }

    $allowedTypes = ['image/jpeg', 'image/png'];
    $maxSize = 5 * 1024 * 1024;

    $tmpName = $file['tmp_name'];
    $fileType = mime_content_type($tmpName);
    $fileSize = $file['size'];

    if (!in_array($fileType, $allowedTypes)) return ['success' => false, 'message' => 'Tipo de arquivo inválido. Apenas JPG e PNG são permitidos.'];
    if ($fileSize > $maxSize) return ['success' => false, 'message' => 'O arquivo é muito grande. O tamanho máximo é 5MB.'];
    
    if (!extension_loaded('gd')) {
        return ['success' => false, 'message' => 'Erro de servidor: A extensão GD para manipulação de imagens não está habilitada.'];
    }

    list($originalWidth, $originalHeight) = getimagesize($tmpName);
    $sourceImage = ($fileType == 'image/jpeg') ? imagecreatefromjpeg($tmpName) : imagecreatefrompng($tmpName);
    
    if (!$sourceImage) {
        return ['success' => false, 'message' => 'Não foi possível ler o arquivo de imagem. Pode estar corrompido.'];
    }
    
    $destImage = imagecreatetruecolor($targetWidth, $targetHeight);

    $cropStartX = 0;
    $cropStartY = 0;
    $cropSize = min($originalWidth, $originalHeight);

    if ($originalWidth > $originalHeight) {
        $cropStartX = (int)(($originalWidth - $originalHeight) / 2);
    } elseif ($originalHeight > $originalWidth) {
        $cropStartY = (int)(($originalHeight - $originalWidth) / 2);
    }

    imagecopyresampled($destImage, $sourceImage, 0, 0, $cropStartX, $cropStartY, $targetWidth, $targetHeight, $cropSize, $cropSize);

    $extension = ($fileType == 'image/jpeg') ? 'jpg' : 'png';
    $newFileName = uniqid('prod_', true) . '.' . $extension;
    $savePath = $uploadDir . $newFileName;

    $saved = ($fileType == 'image/jpeg') ? imagejpeg($destImage, $savePath, 90) : imagepng($destImage, $savePath, 9);

    imagedestroy($sourceImage);
    imagedestroy($destImage);

    if ($saved) {
        return ['success' => true, 'filename' => $newFileName];
    } else {
        return ['success' => false, 'message' => 'Não foi possível salvar a imagem processada no servidor.'];
    }
}