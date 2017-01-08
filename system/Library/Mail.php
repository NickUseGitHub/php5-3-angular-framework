<?php  
namespace Application\Library;

use Application\Library\Dependency\IDependency;
use PHPMailer;

class Mail extends PHPMailer implements IDependency
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct(){}
}