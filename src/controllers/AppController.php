<?php

declare(strict_types=1);

namespace App\Controller;

abstract class AppController
{
    protected function render(string $template, array $variables = []): void
    {
        $templatePath = dirname(__DIR__, 2) . '/public/views/' . $template . '.html.php';

        if (!is_file($templatePath)) {
            throw new \RuntimeException('View not found: ' . $template);
        }

        extract($variables, EXTR_SKIP);

        // Widok trafia do bufora, a wspólny layout wstawia go do <body> jako $content.
        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        require dirname(__DIR__, 2) . '/public/views/layout.html.php';
    }

    protected function renderError(int $status, string $title, string $message): void
    {
        http_response_code($status);

        $this->render('error', [
            'pageTitle' => $title,
            'status' => $status,
            'message' => $message,
        ]);
    }

    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . $path, true, 303);
        exit;
    }
}
