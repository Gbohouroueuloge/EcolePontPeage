<?php

namespace App;


class Router
{
  private $viewPath, $router;

  public function __construct(string $viewPath)
  {
    $this->viewPath = $viewPath;
    $this->router = new \AltoRouter();
  }

  public function get(string $url, string $view, ?string $name = null)
  {
    $this->router->map('GET', $url, $view, $name);
    return $this;
  }

  public function url(string $name, array $params = [])
  {
    return $this->router->generate($name, $params);
  }

  public function run(string $layout = 'default')
  {
    $match = $this->router->match();

    if ($match === false) {
      return $this; // Aucune route correspondante, on laisse le routeur suivant tenter
    }

    $view   = $match['target'];
    $params = $match['params'];
    $router = $this;

    ob_start();
    require $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';
    $content = ob_get_clean();

    require $this->viewPath . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layout . '.php';

    exit;
  }
}
