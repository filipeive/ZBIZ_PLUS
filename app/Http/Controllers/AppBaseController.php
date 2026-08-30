<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelBaseController;
use Illuminate\Http\RedirectResponse;

class AppBaseController extends LaravelBaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Retorna um redirecionamento com uma mensagem de toast.
     *
     * @param string $route Nome da rota (ex: 'products.index')
     * @param string $message Mensagem a ser exibida
     * @param string $type Tipo da mensagem: 'success', 'error', 'warning', 'info'
     * @param array $parameters Parâmetros adicionais para a rota (ex: ['product' => 1])
     * @return RedirectResponse
     */
    protected function redirectWithToast(string $route, string $message, string $type = 'success', array $parameters = []): RedirectResponse
    {
        return redirect()->route($route, $parameters)->with([
            'toast_message' => $message,
            'toast_type' => $type
        ]);
    }

    /**
     * Retorna um redirecionamento de volta (back) com uma mensagem de toast.
     *
     * @param string $message Mensagem a ser exibida
     * @param string $type Tipo da mensagem: 'success', 'error', 'warning', 'info'
     * @return RedirectResponse
     */
    protected function withToast(string $message, string $type = 'success'): RedirectResponse
    {
        return back()->with([
            'toast_message' => $message,
            'toast_type' => $type
        ]);
    }

    /**
     * Retorna um redirecionamento com uma mensagem de sucesso.
     *
     * @param string $route Nome da rota
     * @param string $message Mensagem de sucesso
     * @param array $parameters Parâmetros da rota
     * @return RedirectResponse
     */
    protected function success(string $route, string $message, array $parameters = []): RedirectResponse
    {
        return $this->redirectWithToast($route, $message, 'success', $parameters);
    }

    /**
     * Retorna um redirecionamento com uma mensagem de erro.
     *
     * @param string $route Nome da rota
     * @param string $message Mensagem de erro
     * @param array $parameters Parâmetros da rota
     * @return RedirectResponse
     */
    protected function error(string $route, string $message, array $parameters = []): RedirectResponse
    {
        return $this->redirectWithToast($route, $message, 'error', $parameters);
    }

    /**
     * Retorna um redirecionamento de volta com erro.
     *
     * @param string $message Mensagem de erro
     * @return RedirectResponse
     */
    protected function backWithError(string $message): RedirectResponse
    {
        return $this->withToast($message, 'error');
    }

    /**
     * Retorna um redirecionamento de volta com sucesso.
     *
     * @param string $message Mensagem de sucesso
     * @return RedirectResponse
     */
    protected function backWithSuccess(string $message): RedirectResponse
    {
        return $this->withToast($message, 'success');
    }

    /**
     * Retorna um redirecionamento com aviso (warning).
     *
     * @param string $route Nome da rota
     * @param string $message Mensagem de aviso
     * @param array $parameters Parâmetros da rota
     * @return RedirectResponse
     */
    protected function warning(string $route, string $message, array $parameters = []): RedirectResponse
    {
        return $this->redirectWithToast($route, $message, 'warning', $parameters);
    }

    /**
     * Retorna um redirecionamento com informação (info).
     *
     * @param string $route Nome da rota
     * @param string $message Mensagem informativa
     * @param array $parameters Parâmetros da rota
     * @return RedirectResponse
     */
    protected function info(string $route, string $message, array $parameters = []): RedirectResponse
    {
        return $this->redirectWithToast($route, $message, 'info', $parameters);
    }
}