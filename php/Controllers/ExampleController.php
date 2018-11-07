<?php

namespace Controllers;

use Providers\ExampleProvider;

class ExampleController
{
  public static function example()
  {
    return ExampleProvider::example();
  }
}
