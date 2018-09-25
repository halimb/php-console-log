<?php
/**
 * Log a variable number of PHP variables to the js console
 * @param: (mixed) 
 */
function console_log()
{
    $serialized = var_export(func_get_args(), true);
    $formatted = preg_replace('/(\s.*?::__set_state\()/i', '', $serialized);
    $formatted = preg_replace('/(\)\))/i', ')', $formatted);
    eval('$args =' . $formatted . ';');
    $args_json = json_encode($args);
    echo "<script>
            var jsonDump = JSON.parse('$args_json');
            var consoleArgs = [];
            var indentationStr = '  ';
            var getIndetationLevel = function(str) {
                var openingBraces = (str.match(/\{|\[/gm) || []).length;
                var closingBraces = (str.match(/\}|\]/gm) || []).length;
                return openingBraces - closingBraces;
            };
            
            var jsonString = JSON.stringify(jsonDump)
                    .replace(/null,|\",|[0-9],|\],|\},|\{|\[/gm, function(match, offset, str) {
                        var indentLevel = getIndetationLevel(str.substr(0, offset + 1));
                        var indent = Array(indentLevel).fill(indentationStr).join('');
                        return match + '\\n' + indent;
                    })
                    .replace(/\]|\}/gm, function(match, offset, str) {
                        var indentLevel = getIndetationLevel(str.substr(0, offset + 1));
                        var indent = Array(indentLevel).fill(indentationStr).join('');
                        return '\\n' + indent + match;
                    })
                    .replace(/\"[a-zA-Z0-9_]+?\":|\{|\}|\[|\]|null/gm, 
                        function(match, offset, str){
                            var replacement = '';
                            var lastChar = match[match.length - 1];
                            var indentLevel = getIndetationLevel(str.substr(0, offset + 1));
                            switch (true) {
                                case lastChar === ':':
                                    style = 'color: #3af; font-weight: 600;';
                                    replacement = '%c%s%c ';
                                    break;
                                case lastChar === '{' || lastChar === '}':
                                    style = 'color: #f78c75; font-weight: 900;';
                                    replacement = '%c%s%c';
                                    break;
                                case lastChar === '[' || lastChar === ']':
                                    style = 'color: #dbb027; font-weight: 900;';
                                    replacement = '%c%s%c';
                                    break;
                                default:
                                    style = 'color: #e98500; font-weight: 600;';
                                    replacement = '%c%s%c';
                                    break;
                            }
                            consoleArgs.push(style, match, '');
                            return replacement; 
                        }
                    );
            consoleArgs.unshift(jsonString)
            console.group('PHP LOG');
            console.log.apply(this, consoleArgs);
            console.groupEnd()
        </script>";
}
?>