<?php
/**
 * Dump PHP variables to the js console
 * @param: (mixed) 
 */
function console_log()
{
    $argv = func_get_args();
    $json = __ConsoleLog__::getFormattedJson($argv);

    // add the js helper script if not already included
    if (!__ConsoleLog__::$script_included) {
        echo "<script>" . __ConsoleLog__::getJsPrettyPrintFunction() . "</script>";
        __ConsoleLog__::$script_included = true;
    }

    __ConsoleLog__::callJsPrettyPrint($json);
}

// Wrapper class for console_log
class __ConsoleLog__
{
    public static $script_included = false;

    /**
     * export a variable to a json string
     * @param $argv mixed the parameters to format
     * @return mixed|string
     */
    public static function getFormattedJson($argv)
    {

        if (sizeof($argv) === 1 && is_scalar($argv[0])) {
            $argv = $argv[0];
        } else {
            // Make sure keys are converted to strings to be later formatted in js
            foreach ($argv as $k => $v) {
                $argv[gettype($v)] = $v;
                unset($argv[$k]);
            }

            // <HACK (lines 40 to 57): expose non public object properties and class names
            $argv_str = var_export($argv, true);

            // replace objects identifiers by their respective class names
            $object_identifier = '/((\')?(\S*?)(\')?\s*?=>\s*?(\S*))::__set_state\(/';
            $argv_str = preg_replace_callback(
                $object_identifier,
                function ($matches) {
                    $key = $matches[3];
                    $className = '(' . $matches[sizeof($matches) - 1] . ')';
                    return "'$key:$className' => ";
                },
                $argv_str
            );
            // Remove extra parentheses
            $argv_str = preg_replace('/\)\)/', ')', $argv_str);

            // evaluate the result string (reverse var_export)
            eval('$argv =' . $argv_str . ';');
            // HACK/>
        }

        // export to json
        return json_encode($argv);
    }

    /**
     * echo a script tag containing a call to the js formatter function on the provided json
     * @param $json
     */
    public static function callJsPrettyPrint($json)
    {
        // Call the js formatter on the json
        echo ("<script>
            (function() {
                 var jsonDump = JSON.parse('$json');
                 prettyPrintPHPJson(jsonDump);
            })();
         </script>");
    }

    /**
     * echo the js formatter function
     */
    public static function getJsPrettyPrintFunction()
    {
        echo "<script>
           function prettyPrintPHPJson(jsonDump) {
                var consoleArgs = [],
                indentationStr = '|  ',
                defaultColors = [
                    '#820597',
                    '#3e49a8',
                    '#357FC9',
                    '#2D86AD',
                    '#35b0bc',
                    '#a3af67',
                ],
                styles = {
                    property : {
                        base: 'font-weight: 600;',
                        colorsByIndentLevel: defaultColors
                    },
                    brace : {
                        base: 'color: #555555; font-weight: 600;',
                        colorsByIndentLevel: defaultColors
                    },
                    bracket : {
                        base: 'color: #ba576b; font-weight: 400;',
                        colorsByIndentLevel: defaultColors
                    },
                    nullValue : {
                        base: 'color: #aaa; font-weight: 600;',
                        colorsByIndentLevel: []
                    },
                    pipe: {
                        base: 'color: #00000022; font-size: 70%;font-weight: 100;',
                        colorsByIndentLevel: []
                    }
                },
                regexes = {
                    stylable: /\".+?\":|\{|\}|\[|\]|null|\|/gm,
                    breakAfter: /null,|\",|[0-9],|\],|\},|\{|\[/gm,
                    breakBefore: /\]|\}/gm,
                    argsKey: /(\s{2})\"([0-9]*)\"( →)/gm
                };
        
            function getStyle(type, indentLevel) {
                var baseStyle = styles[type].base;
                var computedStyle = '';
                var color = styles[type].colorsByIndentLevel[indentLevel - 1];
                if (color) {
                    computedStyle += 'color:' + color + ';'
                }
                return baseStyle + computedStyle;
            }
        
            function addStyles(match, offset, str, consoleArgs){
                var replacement = '%c%s%c';
                var lastChar = match[match.length - 1];
                var indentLevel = getIndetationLevel(str.substr(0, offset));
                var closingIndentation = indentLevel ? indentLevel - 1 : 0;
                switch (lastChar) {
                    case ':':
                        var argKey =  /\"(.*?)\":/i;
                        match = match.replace(argKey, indentLevel > 0 ? '$1 → ' : '$1: ');
                        match = match.replace('.', '');
                        style = getStyle('property', indentLevel);
                        break;
                    case '{':
                        style = getStyle('brace', indentLevel);
                        break;
                    case '}':
                        style = getStyle('brace', closingIndentation);
                        break;
                    case '[':
                        style = getStyle('bracket', indentLevel);
                        break;
                    case ']':
                        style = getStyle('bracket', closingIndentation);
                        break;
                    case '|':
                        style = getStyle('pipe');
                        break;
                    default:
                        style = getStyle('nullValue', indentLevel);
                        break;
                }
                consoleArgs.push(style, match, '');
                return replacement;
            }
        
            function getIndetationLevel(str) {
                var openingSymbolsCount = (str.match(/\{|\[/gm) || []).length;
                var closingSymbolsCount = (str.match(/\}|\]/gm) || []).length;
                return openingSymbolsCount - closingSymbolsCount;
            }
        
            function breakAndIndentBefore(match, offset, str, indentationStr) {
                var indentLevel = getIndetationLevel(str.substr(0, offset + 1));
                var indent = getIndent(indentLevel, indentationStr);
                return '\\n' + indent + match;
            }
        
            function breakAndIndentAfter(match, offset, str, indentationStr) {
                var indentLevel = getIndetationLevel(str.substr(0, offset + 1));
                var indent = getIndent(indentLevel, indentationStr);
                return match + '\\n' + indent;
            }
        
            function getIndent(level, str) {
                return Array(level).fill(str).join('');
            }
        
            var jsonString = JSON.stringify(jsonDump).replace(/^\{|\}$/gm, '')
                .replace(regexes.breakAfter, function(match, offset, str) { return breakAndIndentAfter(match, offset, str, indentationStr) })
                .replace(regexes.breakBefore, function(match, offset, str) { return breakAndIndentBefore(match, offset, str, indentationStr)})
                .replace(regexes.stylable, function(match, offset, str) { return addStyles(match, offset, str, consoleArgs) });
        
        
            consoleArgs.unshift(jsonString);
            console.group('PHP');
            console.log.apply(this, consoleArgs);
            console.groupEnd();
        }
        </script>";
    }
}
?>