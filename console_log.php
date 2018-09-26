<?php
/**
 * Log a variable number of PHP variables to the js console
 * @param: (mixed) 
 */
function console_log()
{
    $argv = array();
    foreach(func_get_args() as $var_key => $value) {
        $argv['__ARG__' . $var_key] = $value;
    }
    $serialized = var_export($argv, true);
    $formatted = preg_replace('/(\s.*?::__set_state\()/i', '', $serialized);
    $formatted = preg_replace('/(\)\))/i', ')', $formatted);
    eval('$args =' . $formatted . ';');
    $args_json = json_encode($args);

    echo "<script>
             var jsonDump = JSON.parse('$args_json'),
                consoleArgs = [],
                indentationStr = '  ',
                regexes = {
                    stylable: /\"[a-zA-Z0-9_]+?\":|\{|\}|\[|\]|null/gm,
                    breakAfter: /null,|\",|[0-9],|\],|\},|\{|\[/gm,
                    breakBefore: /\]|\}/gm,
                },
                styles = {
                   property : {
                       base: 'font-weight: 600;',
                       colorsByIndentLevel: [
                          '#444', 
                          '#519DD2',
                          '#3F789E',
                          '#1BA5D2', 
                          '#69b6ba', 
                          '#ace383', 
                          '#D2A283'
                       ]
                   },
                   brace : {
                       base: 'color: #D79F49; font-weight: 600;',
                       colorsByIndentLevel: []
                   },
                   bracket : {
                       base: 'color: #ba576b; font-weight: 600;',
                       colorsByIndentLevel: []
                   },
                   nullValue : {
                       base: 'color: #e98500; font-weight: 600;',
                       colorsByIndentLevel: []
                   },
                };
             
             function getStyle(type, indentationLevel) {
                 var baseStyle = styles[type].base;
                 var computedStyle = '';
                 var color = styles[type].colorsByIndentLevel[indentationLevel - 1];
                 if (color) {
                     computedStyle += 'color:' + color + ';'
                 }
                 return baseStyle + computedStyle;
             }
            
             function addStyles(match, offset, str, consoleArgs){
                var replacement = '%c%s%c';
                var lastChar = match[match.length - 1];
                var indentationLevel = getIndetationLevel(str.substr(0, offset));
                var closingIndentation = indentationLevel ? indentationLevel - 1 : 0;
                switch (lastChar) {
                    case ':':
                        replacement = '%c%s%c ';
                        style = getStyle('property', indentationLevel);
                        break;
                    case '{':
                        style = getStyle('brace', indentationLevel);
                        break;
                    case '}':
                        style = getStyle('brace', closingIndentation);
                        break;
                    case '[':
                        style = getStyle('bracket', indentationLevel);
                        break;
                    case ']':
                        style = getStyle('bracket', closingIndentation);
                        break;
                    default:
                        style = getStyle('nullValue', indentationLevel);
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
                var indent = Array(indentLevel).fill(indentationStr).join('');
                return '\\n' + indent + match;
            }
            
            function breakAndIndentAfter(match, offset, str, indentationStr) {
                var indentLevel = getIndetationLevel(str.substr(0, offset + 1));
                var indent = Array(indentLevel).fill(indentationStr).join('');
                return match + '\\n' + indent;
            }
            
            function preprocessJson(json) {
                json = json.length === 1 ? json[0] : json;
            
                if (typeof json == 'object') {
                    Object.keys(json).forEach(function(key) {
                        if (/__ARG__/i.test(key)) {
                            var numKey = parseInt(key.replace(/__ARG__/i, ''));
                            if (numKey !== undefined) {
                                json[numKey] = json[key];
                                delete json[key];
                            }
                        }
                    })
                }
            }
            
            preprocessJson(jsonDump);
            
            var jsonString = JSON.stringify(jsonDump)
                    .replace(regexes.breakAfter, function(match, offset, str) { return breakAndIndentAfter(match, offset, str, indentationStr) })
                    .replace(regexes.breakBefore, function(match, offset, str) { return breakAndIndentBefore(match, offset, str, indentationStr)})
                    .replace(regexes.stylable, function(match, offset, str) { return addStyles(match, offset, str, consoleArgs) });
            
            consoleArgs.unshift(jsonString)
            console.group('PHP LOG');
            console.log.apply(this, consoleArgs);
            console.groupEnd();
        </script>";
}
?>