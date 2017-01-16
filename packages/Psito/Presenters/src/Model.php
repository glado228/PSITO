<?php

namespace Psito\Presenters;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent implements PresentableInterface
{
    use PresentableTrait;
}
