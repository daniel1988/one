<?php
namespace OneLib;
class Cli
{

    /**
     * CLI 命令行函数
     */
    /**
     * 生成具有颜色的文本
     *
     * @param  string   $text
     * @param  string   $foreground 前景色
     * @param  string   $background 背景色
     * @return string
     */
    static function color_text($text, $foreground = null, $background = null)
    {
        // 可选的文本前景色列表
        static $foregroundColors = [
            'black'        => '0;30',
            'dark_gray'    => '1;30',
            'blue'         => '0;34',
            'light_blue'   => '1;34',
            'green'        => '0;32',
            'light_green'  => '1;32',
            'cyan'         => '0;36',
            'light_cyan'   => '1;36',
            'red'          => '0;31',
            'light_red'    => '1;31',
            'purple'       => '0;35',
            'light_purple' => '1;35',
            'brown'        => '0;33',
            'yellow'       => '1;33',
            'light_gray'   => '0;37',
            'white'        => '1;37',
        ];

        // 可选的文本背景色列表
        static $backgroundColors = [
            'black'      => '40',
            'red'        => '41',
            'green'      => '42',
            'yellow'     => '43',
            'blue'       => '44',
            'magenta'    => '45',
            'cyan'       => '46',
            'light_gray' => '47',
        ];

        // 定义常见格式字符
        $defines = [
            'error'   => ['white', 'red'],
            'success' => ['white', 'green'],
            'warning' => ['white', 'yellow'],
            'info'    => ['white', 'blue'],
            'notice'  => ['green', null],
            'danger'  => ['red', null],
            'import'  => ['light_cyan', null],
            'quote'   => ['purple', null],
        ];

        if ($foreground && isset($defines[$foreground])) {
            $background = $defines[$foreground][1];
            $foreground = $defines[$foreground][0];
        }

        $string = '';

        if (isset($foregroundColors[$foreground])) {
            $string .= "\033[" . $foregroundColors[$foreground] . 'm';
        }

        if (isset($backgroundColors[$background])) {
            $string .= "\033[" . $backgroundColors[$background] . 'm';
        }

        $string .= $text . "\033[0m";

        return $string;
    }

    /**
     * 等待 STDIN 输入字符并捕获
     *
     * @param  boolean  $raw 是否返回原始字符串，否则自动去除多余的换行
     * @return string
     */
    static function cli_stdin($raw = false)
    {
        return $raw ? fgets(STDIN) : rtrim(fgets(STDIN), PHP_EOL);
    }

    /**
     * 输出文本到 STDOUT
     *
     * @param  string    $text
     * @param  boolean   $appendEOL
     * @return integer
     */
    static function cli_stdout($text = null, $appendEOL = true)
    {
        return fwrite(STDOUT, $text . ($appendEOL ? PHP_EOL : ''));
    }

    /**
     * 输出文本到 STDERR
     *
     * @param  string    $text
     * @param  boolean   $appendEOL
     * @return integer
     */
    static function cli_stderr($text = null, $appendEOL = true)
    {
        return fwrite(STDERR, $text . ($appendEOL ? PHP_EOL : ''));
    }

    /**
     * 等待用户输入文本并捕获
     *
     * @param  string   $prompt
     * @return string
     */
    static function cli_input($prompt = null)
    {
        if (isset($prompt)) {
            cli_stdout($prompt, false);
        }

        return cli_stdin();
    }

    /**
     * 输出一行文本到 STDOUT
     *
     *     cli_output('hello', 'red', 'green');
     *     cli_output('yes!', 'error');
     *     cli_output('no!', 'success');
     *
     * @param  string    $text
     * @param  string    $foreground
     * @param  string    $background
     * @return integer
     */
    static function cli_output($text = null, $foreground = null, $background = null, $appendEOL = true)
    {
        if ($text === null) {
            return fwrite(STDOUT, ($appendEOL ? PHP_EOL : ''));
        }

        return cli_stdout(color_text($text, $foreground, $background), $appendEOL);
    }

    /**
     * 输出错误信息，并中断所有操作
     *
     * @param string  $text
     * @param integer $code
     */
    static function cli_error($text, $code = 255)
    {
        cli_output($text, 'error');
        exit($code);
    }

    /**
     * 提示并等待用户输入
     *
     *     选项：
     *         required  是否必须值，默认为 false
     *         default   默认值
     *         pattern   正则验证表达式，默认无限制
     *         validator 回调验证函数/方法
     *         error     输入错误，提示文本
     *
     * @param  string    $text
     * @param  array     $options
     * @return string
     */
    static function cli_prompt($text, $options = [])
    {
        $options = $options + [
            'required'  => false,
            'default'   => null,
            'pattern'   => null,
            'validator' => null,
            'error'     => '输入错误',
        ];

        $input = cli_input($text . ($options['default'] ? " [$options[default]]" : '') . ': ');
        $error = null;

        if (!strlen($input)) {
            if (isset($options['default'])) {
                $input = $options['default'];
            } elseif ($options['required']) {
                cli_error($options['error']);

                return cli_prompt($text, $options);
            }
        } elseif ($options['pattern'] && !preg_match($options['pattern'], $input)) {
            cli_error($options['error']);

            return cli_prompt($text, $options);
        } elseif ($options['validator'] && !call_user_func_array($options['validator'], [$input, &$error])) {
            cli_error($error ?: $options['error']);

            return cli_prompt($text, $options);
        }

        return $input;
    }

    /**
     * 询问用户，并要求输入 y/n 确认
     *
     * @param  string    $text
     * @return boolean
     */
    static function cli_confirm($text)
    {
        $input = strtolower(cli_input("$text [y/n]: "));
        if (!in_array($input, ['y', 'n'])) {
            return cli_confirm($text);
        }

        return $input === 'y' ? true : false;
    }

    /**
     * 创建一个选择列表，并捕获用户的选择
     *
     *     cli_select('please select:', array(
     *         'a' => 'the first option',
     *         'b' => 'the second option',
     *         'c' => 'the third option',
     *         'd' => 'the fourth option',
     *     ));
     *
     * @param  string    $text
     * @param  array     $options
     * @return string
     */
    static function cli_select($text, array $options)
    {
        echo '-------------------------------------' . PHP_EOL;
        foreach ($options as $key => $value) {
            echo "  $key - $value" . PHP_EOL;
        }
        echo '-------------------------------------' . PHP_EOL;

        while (true) {
            $input = cli_input("$text [" . implode(', ', array_keys($options)) . ']: ');
            if (isset($options[$input])) {
                return $input;
            }
        }
    }


}