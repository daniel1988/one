<?php
namespace OneLib;

class String
{


    /**
     * 字符/数字相关函数
     */
    /**
     * 获取某个浮点数的 digit，比如 12.39719 返回 5
     *
     * @param  float $number
     * @return int
     */
    static function get_digits($number)
    {
        return strlen(preg_replace('/\d+\./', '', $number));
    }

    /**
     * 因 number_format 默认参数带来的千分位是逗号的hack
     * 功能和 number_format一致，只是设定了固定的第3，4个参数
     *
     * @param  mixed   $number
     * @param  integer $decimals
     * @param  string  $thousands_sep
     * @return mixed
     */
    static function num_format($number, $decimals = 0, $thousands_sep = ',')
    {
        return number_format($number, $decimals, '.', $thousands_sep);
    }

    /**
     * 按指定的长度切割字符串
     *
     * @param  string   $string 需要切割的字符串
     * @param  integer  $length 长度
     * @param  string   $suffix 切割后补充的字符串
     * @return string
     */
    static function str_break($string, $length, $suffix = '')
    {
        if (strlen($string) <= $length + strlen($suffix)) {
            return $string;
        }

        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if (9 == $t || 10 == $t || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t < 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif (252 == $t || 253 == $t) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length) {
                break;
            }
        }
        $noc > $length && $n -= $tn;
        $strcut = substr($string, 0, $n);
        if (strlen($strcut) < strlen($string)) {
            $strcut .= $suffix;
        }

        return $strcut;
    }

    /**
     * 字符串高亮
     *
     * @param  string   $string  需要的高亮的字符串
     * @param  mixed    $keyword 关键字，可以是一个数组
     * @return string
     */
    static function highlight_keyword($string, $keyword)
    {
        $string = (string) $string;

        if ($string && $keyword) {
            if (!is_array($keyword)) {
                $keyword = [$keyword];
            }

            $pattern = [];
            foreach ($keyword as $word) {
                if (!empty($word)) {
                    $pattern[] = '(' . str_replace('/', '\/', preg_quote($word)) . ')';
                }
            }

            if (!empty($pattern)) {
                $string = preg_replace(
                    '/(' . implode('|', $pattern) . ')/is',
                    '<span style="background:#FF0;color:#E00;">\\1</span>',
                    $string
                );
            }
        }

        return $string;
    }

    /**
     * 将 HTML 转换为文本
     *
     * @param  string   $html
     * @return string
     */
    static function html2txt($html)
    {
        $html = trim($html);
        if (empty($html)) {
            return $html;
        }

        $search = ["'<script[^>]*?>.*?</script>'si",
            "'<style[^>]*?>.*?</style>'si",
            "'<[\/\!]*?[^<>]*?>'si",
            "'([\r\n])[\s]+'",
            "'&(quot|#34);'i",
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160)[;]*'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&#(\d+);'e",
        ];
        $replace = ['', '', '', '\\1', '"', '&', '<', '>', ' ',
            chr(161), chr(162), chr(163), chr(169), 'chr(\\1)'];

        return preg_replace($search, $replace, $html);
    }

    static function lcwords($string)
    {
        $tokens = explode(' ', $string);
        if (!is_array($tokens) || count($tokens) <= 1) {
            return lcfirst($string);
        }

        $result = [];
        foreach ($tokens as $token) {
            $result[] = lcfirst($token);
        }

        return implode(' ', $result);
    }

    /**
     * 转换为 utf8 编码
     *
     * @param  mixed   $data          需要转换的数据
     * @param  string  $from_encoding 来源编码类型，默认自动检测类型
     * @return mixed
     */
    static function to_utf8($data, $from_encoding = 'gbk')
    {
        return to_encoding($data, 'utf-8', $from_encoding);
    }

    /**
     * 转换为 gbk 编码
     *
     * @param  mixed   $data          需要转换的数据
     * @param  string  $from_encoding 来源编码类型，默认自动检测类型
     * @return mixed
     */
    static function to_gbk($data, $from_encoding = 'utf-8')
    {
        // 转化成 gbk 存在特殊的繁体中文字符无法识别的情况，这里用gbk的编码
        return to_encoding($data, 'gbk', $from_encoding);
    }

    /**
     * 支持对多维数组，对象，... 进行编码转换
     * 在不指定来源编码时，由系统自动检测编码类型
     *
     * @link   http://cn2.php.net/manual/zh/static function.mb-detect-encoding.php
     * @link   http://cn2.php.net/manual/zh/static function.mb-detect-order.php
     *
     * @param  mixed    $data          需要转换的数据
     * @param  string   $to_encoding   目标编码类型
     * @param  string   $from_encoding 来源编码类型，默认自动检测类型
     * @param  mixed    $encoding_list 编码检测类型及顺序
     * @return mixed
     */
    static function to_encoding($data, $to_encoding, $from_encoding = null,
        $encoding_list = 'UTF-8,GBK,CP936,ISO-8859-1,ASCII') {
        if (is_string($data)) {
            if (null === $from_encoding) {
                $from_encoding = mb_detect_encoding($data, $encoding_list);
            }

            if (strtoupper($to_encoding) !== strtoupper($from_encoding)) {
                $data = mb_convert_encoding($data, $to_encoding, $from_encoding);
            }
        } elseif (is_array($data) || is_object($data)) {
            foreach ($data as $key => &$val) {
                $val = call_user_func_array(__FUNCTION__, [$val, $to_encoding, $from_encoding]);
            }
        }

        return $data;
    }

    /**
     * 转换驼峰式字符串为下划线风格
     *
     *     uncamel('lowerCamelCase') === 'lower_camel_case'
     *     uncamel('UpperCamelCase') === 'upper_camel_case'
     *     uncamel('ThisIsAString') === 'this_is_a_string'
     *     uncamel('notcamelcase') === 'notcamelcase'
     *     uncamel('lowerCamelCase', ' | ') === 'lower | camel | case'
     *
     * @param  string    $string
     * @param  string    $separator
     * @return string
     */
    static function uncamel($string, $separator = '_')
    {
        return str_replace('_', $separator, Phalcon\Text::uncamelize($string));
    }

    /**
     * 转换下划线字符串为驼峰式风格
     *
     *     camel('lower_camel_case') === 'lowerCamelCase'
     *     camel('upper_camel_case', true) === 'UpperCamelCase'
     *
     * @param  string   $string
     * @param  string   $upper
     * @return string
     */
    static function camel($string, $upper = false, $separator = '_')
    {
        $string = str_replace($separator, '_', $string);

        return $upper ? Phalcon\Text::camelize($string) : lcfirst(Phalcon\Text::camelize($string));
    }

    /**
     * RMB 数字转大写
     *
     * @param  string   $num
     * @return string
     */
    static function number_to_rmb($num)
    {
        if ('0' == $num) {
            return '零';
        }

        $chs = ['0', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
        $uni = ['', '拾', '佰', '仟'];
        $exp = ['', '万'];
        $num .= '';
        $res = '';
        for ($i = strlen($num) - 1, $k = 0; $i >= 0; $k++) {
            $str = '';
            // 按照中文读写习惯，每4个字为一段进行转化
            for ($j = 0; $j < 4 && $i >= 0; $j++, $i--) {
                $u   = $num{$i} > 0 ? $uni[$j] : ''; // 非0的数字后面添加单位
                $str = $chs[$num{$i}] . $u . $str;
            }

            $str = preg_replace('/0+$/', '', $str);   // 去掉末尾的0
            $str = preg_replace('/0+/', '零', $str); // 替换0

            if (!isset($exp[$k])) {
                $exp[$k] = $exp[$k - 2] . '亿'; //构建单位
            }

            $u2  = '' != $str ? $exp[$k] : '';
            $res = $str . $u2 . $res;
        }

        return $res;
    }

    /**
     * 根据中国身份证号码获得出生日期
     *
     * @param  string   $idNumber
     * @return string
     */
    static function cn_id_to_birthday($idNumber)
    {
        if (strlen($idNumber) != 18) {
            return false;
        }

        return substr($idNumber, 6, 4) . '/' .
        substr($idNumber, 10, 2) . '/' .
        substr($idNumber, 12, 2);
    }

    /**
     * 根据 索引 返回对应的 map
     * @param  array $maps
     * @param  mix   $index
     * @return mix
     */
    static function maps_get($maps, $index)
    {
        return (is_null($index) || !isset($maps[$index])) ? '-' : $maps[$index];
    }

    /**
     * 隐藏用户名
     *
     * @param  string   $name
     * @param  string   $hide
     * @return string
     */
    static function mask_name($name, $hide = '****')
    {
        if (!$name) {
            return '-';
        }

        return mb_substr($name, 0, 1) . $hide . mb_substr($name, -1, 1);
    }

    /**
     * 隐藏银行卡号
     *
     * @param  string   $name
     * @param  string   $hide
     * @return string
     */
    static function mask_bank_card($name, $hide = '****')
    {
        if (!$name) {
            return '-';
        }

        return $hide . mb_substr($name, -4, 4);
    }

    /**
     * 隐藏电话号码
     *
     * @param  string   $phone
     * @param  string   $hide
     * @return string
     */
    static function mask_phone($phone, $hide = '****')
    {
        if (!$phone) {
            return '-';
        }

        $pos = strpos($phone, ' ');

        return mb_substr($phone, 0, (false === $pos ? 3 : $pos + 4))
        . $hide . mb_substr($phone, -4, 4);
    }

    /**
     * 隐藏 Email
     *
     * @param  string   $email
     * @param  string   $hide
     * @return string
     */
    static function mask_email($email, $hide = '****')
    {
        if (!$email) {
            return '-';
        }

        return mb_substr($email, 0, 2) . $hide . strstr($email, '@');
    }

    /**
     * 隐藏证件号码
     *
     * @param  string   $id_number
     * @param  string   $hide
     * @return string
     */
    static function mask_certificates($id_number, $hide = '****')
    {
        if (!$id_number) {
            return '-';
        }

        return mb_substr($id_number, 0, 6) . $hide;
    }

    /**
     * 格式化文件大小显示
     *
     * @param  int      $size
     * @return string
     */
    static function format_file_size($size)
    {
        $units = ['Byte', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB'];

        foreach ($units as $unit) {
            if ($size < 1024) {
                return round($size, 2) . " $unit";
            }

            $size /= 1024;
        }

        return '尼玛，哪有这么大的文件！？';
    }

    /**
     * 是否为base64字符串
     * @param  [type]  $str [description]
     * @return boolean      [description]
     */
    static function is_base64_str($str)
    {
        if (empty($str)) {
            return false;
        }

        return (base64_encode(base64_decode($str)) == $str) ? true : false;
    }
}
