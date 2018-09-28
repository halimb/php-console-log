# PHP → console.log

Dump PHP variables to the browser console

### Installation
Simply download the file and include() it in your project.

### Examples
<style>
	.code-block{
    	display: inline-block;
    }
    .tab::after{
    	content: '';
        margin-right: 50px;
    }
</style>

| PHP  | Browser console |
| ---- | --------------- |
| **Scalar**<br> <code class="code-block">$a = 42;<br>console_log($a);</code> | <img src="https://raw.githubusercontent.com/halimb/php-console-log/master/img/scalar.png"/>|
||
|  **Map**<br><code class="code-block">$b&nbsp;=&nbsp;array(<br>&nbsp;&nbsp;'foo'&nbsp;=>&nbsp;'some&nbsp;value',<br>&nbsp;&nbsp;'bar'&nbsp;=>&nbsp;[<br>&nbsp;&nbsp;&nbsp;&nbsp;'baz'&nbsp;=>&nbsp;113,<br>&nbsp;&nbsp;&nbsp;&nbsp;'qux'&nbsp;=>&nbsp;null,<br>&nbsp;&nbsp;&nbsp;&nbsp;'corge'&nbsp;=>&nbsp;[0,1,2]<br>&nbsp;&nbsp;]<br>);</code> | <img src="https://raw.githubusercontent.com/halimb/php-console-log/master/img/array.png"/>|
||
|  **Object**<br><code class="code-block">class&nbsp;SomeObject<br>{<br>&nbsp;&nbsp;private&nbsp;$grault;<br><br>&nbsp;&nbsp;public&nbsp;function&nbsp;__construct($val)<br>&nbsp;&nbsp;{<br>&nbsp;&nbsp;&nbsp;&nbsp;$this->grault&nbsp;=&nbsp;$val;<br>&nbsp;&nbsp;}<br>}<br><br>class&nbsp;TestClass<br>{<br>&nbsp;&nbsp;public&nbsp;$public_var;<br>&nbsp;&nbsp;private&nbsp;$private_var;<br>&nbsp;&nbsp;protected&nbsp;$protected_var;<br>&nbsp;&nbsp;private&nbsp;$object_var;<br><br>&nbsp;&nbsp;public&nbsp;function&nbsp;__construct(<br>&nbsp;&nbsp;&nbsp;&nbsp;$public,&nbsp;<br>&nbsp;&nbsp;&nbsp;&nbsp;$private,&nbsp;<br>&nbsp;&nbsp;&nbsp;&nbsp;$protected<br>&nbsp;&nbsp;)<br>&nbsp;&nbsp;{<br>&nbsp;&nbsp;&nbsp;&nbsp;$this->public_var&nbsp;=&nbsp;$public;<br>&nbsp;&nbsp;&nbsp;&nbsp;$this->private_var&nbsp;=&nbsp;$private;<br>&nbsp;&nbsp;&nbsp;&nbsp;$this->protected_var&nbsp;=&nbsp;$protected;<br>&nbsp;&nbsp;&nbsp;&nbsp;$this->object_var&nbsp;=&nbsp;new&nbsp;SomeObject(42);<br>&nbsp;&nbsp;}<br>}<br><br>$testClass&nbsp;=&nbsp;new&nbsp;TestClass(<br>&nbsp;&nbsp;'public&nbsp;value',<br>&nbsp;&nbsp;'private&nbsp;value',<br>&nbsp;&nbsp;'protected&nbsp;value'<br>);<br><br>console_log($testClass);<br></code> | <img src="https://raw.githubusercontent.com/halimb/php-console-log/master/img/object.png"/>|
||
|  **Multiple**<br><code class="code-block">console_log($a, $b, $testClass);</code> | <img src="https://raw.githubusercontent.com/halimb/php-console-log/master/img/multiple.png"/>|

