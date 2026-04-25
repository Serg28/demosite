<?php
namespace Litvin\Redirectmap;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Litvin\Redirectmap\Models\RedirectMap;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Decorator implements ExceptionHandler
{
    protected $handler;
    /**
     * Set the dependencies.
     *
     * @param    Illuminate\Contracts\Debug\ExceptionHandler    $handler
     * @return    void
     */
    public function __construct(ExceptionHandler $handler)
    {
        $this->handler  = $handler;
    }
    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Throwable $e)
    {
        $this->handler->report($e);
    }
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
            $path = $request->getRequestUri();
            $map = RedirectMap::where('old_link', $path)->first();

            if ($map) {

                $status = $map->status ? : 301;

                return \Response::redirectTo($map->new_link, $status);
            }
        }

        return $this->handler->render($request, $e);
    }
    /**
     * Render an exception to the console.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Exception  $e
     * @return void
     */
    public function renderForConsole($output, Throwable $e)
    {
        $this->handler->renderForConsole($output, $e);
    }

    public function shouldReport(Throwable $e)
    {
        $this->handler->shouldReport($e);
    }
}
