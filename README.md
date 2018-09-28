# PHP → console.log

Dump PHP variables to the browser console

# Installation
Simply download the file and include() it in your project.

# Examples


## Scalar

**php:**

```php
$a = 42;
console_log($a);
```  
**Browser console output:**

![scalar example](https://raw.githubusercontent.com/halimb/php-console-log/master/img/scalar.png)



## Map

**php:**
  
```php
$b = array(  
    'foo' => 'some value',  
    'bar' => [
        'baz' => 113,
        'qux' => null,
        'corge' => [0,1,2]  
    ]
);
console_log($b);
```  
**Browser console output:**

![array example](https://raw.githubusercontent.com/halimb/php-console-log/master/img/array.png)



## Object

**php:**

```php
class SomeObject
{
    private $grault;

    public function __construct($val)
    {
        $this->grault = $val;
    }
}

class TestClass
{
    public $public_var;
    private $private_var;
    protected $protected_var;
    private $object_var;

    public function __construct($public, $private, $protected)
    {
        $this->public_var = $public;
        $this->private_var = $private;
        $this->protected_var = $protected;
        $this->object_var = new SomeObject(42);
    }
}

$testClass = new TestClass('public value','private value', 'protected value');
console_log($testClass);
```  
**Browser console output:**

![object example](https://raw.githubusercontent.com/halimb/php-console-log/master/img/object.png)


## Multiple

**php:**

```php
console_log($a, $b, $testClass);
```  
**Browser console output:**

![multiple example](https://raw.githubusercontent.com/halimb/php-console-log/master/img/multiple.png)

