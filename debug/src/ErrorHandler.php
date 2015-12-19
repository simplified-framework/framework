<?php

namespace Simplified\Debug;

use Simplified\Http\Response;
use Simplified\Log\Logger;

class ErrorHandler {
    private static $logger;
    private static $errorSet = false;
    private static $css = '
        <style type="text/css">
		body {
			font-family: arial, verdana,sans;
			font-size: 12px;
			background-color: #f0f0f0;
			padding: 20px;
		}
		.wrapper {
			margin: 0 auto;
			width: 960px;
			background-color: #f9f9f9;
			padding: 20px;
			border-radius: 5px;
			border: 1px solid #DDD;
		}
		.message {
			background-color: #CCC;
			color: #333;
			padding: 10px;
			font-size: 14px;
			font-weight: bold;
			border-radius: 5px;
		}
		.backtrace {
			font-size: 14px;
			padding: 10px;
		}
		.backtrace .filecontent {
			padding: 10px;
			background-color: #eee;
		}
		table {
			border: 1px solid #ddd;
			width: 100%;
			margin-top: 10px;
		}
		td {
			font-size: 12px;
			padding: 4px;
			color: #444;
		}
		td.line {
			background-color: #fff;
			font-family: monospace;
			padding-left: 20px;
		}
		.fileline {
			background-color: #EFEFEF;
			width: 30px;
			text-align: center;
			font-size: 11px;
		}
		.lineinfo {
			color: #666;
		}
		h3 {
			padding-left: 60px;
			background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAABAxJREFUaIHVms+LU1cUx7/nvFBrJ1ILFkFwZcx9k0k1WGzrTjfdtxXbDphVW0a7Kf5oN0IL7qQd6KJ0wJUDFQmlpf+AuipFHJSxJO+9yOykIhaFmYyl5p3TxbxMk5mJeb+SN/OBQO6Pd+73JDfvnXtOCCnQMGYPgGNQfQeAEeZ9LPIagHwwZUmYn7DIfWV2SfUPJbpRcpy/kq5NcS/0CoXXfcuahGoVzIdimpmD6iypXrU973EcA5EduDcxsTf3/PmXwvwJAy/HWXQtAjwj4LLl+5dMs/kgyrWhHfAKhW2+ZZ0VogsMbI8uMwQiy8p8UZiny/X6v2EuCeWAUyzaPnONgTeSKQyHAPOsemLcdd1Bc3nQhIYxxxW4PSrxAMDAAajO1W37/RBz+1O37c+FqAbmsfTkhYR5TIGfG8ZMvWha3y1Ut+3TBPyQvrIYqJ4ad92ZjYY2dKBhzHEhqnGC22yaCKAEfFBynF/Xjq0T6BSLtgK3k2ybXKWCsenpnr7WmTNo370b1yQgsqTMb5Ycx+vu7vkNeIXCNp85mz0/COY8gNqfpdJLPd3dDd+yzo7ybhMVAg6S6hfdfasO3JuY2CtEF0YvKxrs+18HsddKu/PG8v3zQ3vCpgnzKwDOrTaBlcBMgU8zExURIZpyisVdQOCAb1mTaQVmo4CB7cL8UfAegGo1U0XxqAJArmHMHhDFjeczg4DD8+XybgZwLGsxcbF8/ygHx8AtCakeYQAmayEJMCzM+7JWkYD9HGQPtiQkspPxf+pjK7Jj4JFys8MAlrIWkYBFFuYnWauIizI/ZRa5n7WQBDRzyuwS8G6aVuXhQ/xz5cq6viHgUsOYSRD9NAzrw4ZEPmQlupG1kLjk2u2bHKS457IWExUFbhUWFh51zgOzGeuJwywQHGhI9aoAz9KynKtU8Or16z2vXKWSlnlAZBnANSBwwPa8xwRcTm+FIUM0U3Kcv4HerMSlwLPNjUhLib7tNFcdMM3mA2W+mI2qCBB9011by3WPCfM0iXzMwIEkawztQaZ6p5XPf9/dtS652zDGQHVus+VHBVhU5kPler0n9FkXTo+7rqvMVQF0dPJejABiiZxcKx7oU6EpOc4vrHp6+NLCQcCU7Xm/bTTW90Az7rozUD2V5TchgCjwWclx+t7iB1Zg6rb9HonMBvn5kSHAoiVyst8n3yFUCalu20UANQIOpqJuEKp3fMs6sdGeX0uoM3HJcTxhfkuIvhrqw06kBdXzrXz+7TDigRhFvKC4cE6IplKrJ4gsC/OPBHwX9Q8gsauQTrG4K0hxVwk4HMeGArewElVe68Q2UUmljDpfLu+2fP8oqR7BSqpyP4nsBLAjmLKozE8BNAG4LPJ7rt2+WVhYeJR07f8AnKV9oVNT5JkAAAAASUVORK5CYII=") no-repeat left center;
			height: 48px;
			line-height: 48px;
			margin-top: 0;
			font-size: 16px;
			color: #222;
		}
		</style>
    ';

    public static function handleException(\Exception $e) {
        if( self::$errorSet )
            return;

        $stacktrace = array();
        $trace = $e->getTrace();

        if (self::$logger == null)
            self::$logger = new Logger();

        self::$logger->error($e->getMessage());

        foreach($trace as $t) {
            $line = '';
            if( !empty($t['file']) ) {
                $tmp = array();
                $func = !empty($t['class']) ? $t['class'].$t['type'].$t['function'].'()' : $t['function'];
                $tmp[] = '<strong>at function '. $func.'</strong>';
                $tmp[] = 'in file ' . $t['file'] . ', Line ' . $t['line'];
                $line = implode('<br>', $tmp);
                $filecontent = self::readFileLines($t['file'], $t['line'], $t['function']);
                $line.= '<div class="filecontent">'.$filecontent.'</div>';
            } else {
                $func = !empty($t['class']) ? $t['class'].$t['type'].$t['function'].'()' : $t['function'];
                $line = '<strong>called from '. $func.'</strong>';
            }
            $stacktrace[] = $line;
        }

        $stacktrace = implode('<br>', $stacktrace);

        $html = '
		<html>
		<head><title>Exception</title>'.self::compressCss().'</head>
		<body>
		<div class="wrapper">
		<h3>Uncaught '.get_class($e).' on Server '.$_SERVER['SERVER_NAME'].' at port '.$_SERVER['SERVER_PORT'].'</h3>
		<div class="message">'.$e->getMessage().'</div>
		<h4>Stack Trace:</h4>
		<div>
		'.$stacktrace.'
		</div>
		</div>
		</body>
		</html>
		';

        self::output($html, $e->getMessage());
        exit;
    }

    public static function handleError($errno, $errstr, $errfile, $errline) {

        self::$errorSet = true;
        $stacktrace = array();
        $trace = debug_backtrace();

        if (self::$logger == null) {
            self::$logger = new Logger();
        }
        self::$logger->error($errstr);

        foreach($trace as $t) {
            $line = '';
            if( !empty($t['file']) ) {
                $tmp = array();
                $func = !empty($t['class']) ? $t['class'].$t['type'].$t['function'].'()' : $t['function'];
                $tmp[] = '<strong>at function '. $func.'</strong>';
                $tmp[] = 'in file ' . $t['file'] . ', Line ' . $t['line'];
                $line = implode('<br>', $tmp);
                $filecontent = self::readFileLines($t['file'], $t['line'], $t['function']);
                $line.= '<div class="filecontent">'.$filecontent.'</div>';
            } else {
                $func = !empty($t['class']) ? $t['class'].$t['type'].$t['function'].'()' : $t['function'];
                $line = '<strong>called from '. $func.'</strong>';
            }
            $stacktrace[] = $line;

        }

        $stacktrace = implode('<br>', $stacktrace);

        $html = '
		<html>
		<head><title>Exception</title>'.self::compressCss().'</head>
		<body>
		<div class="wrapper">
		<h3>Error on Server '.$_SERVER['SERVER_NAME'].' at port '.$_SERVER['SERVER_PORT'].'</h3>
		<div class="message">'.$errstr.'</div>
		<h4>Stack Trace:</h4>
		<div>
		'.$stacktrace.'
		</div>
		</div>
		</body>
		</html>
		';

        self::output($html, $errstr);
        exit;
    }

    public static function handleShutdown() {
        $isError = false;

        if ($error = error_get_last()){
            switch($error['type']){
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                case E_PARSE:
                    $isError = true;
                    break;
            }
        }

        if( $isError ) {
            self::$errorSet = true;
            $stacktrace = array();
            $trace = debug_backtrace();
            $trace = array_reverse($trace);

            if (self::$logger == null) {
                self::$logger = new Logger();
            }
            self::$logger->error($error['message']);

            foreach($trace as $t) {
                $line = '';
                if( !empty($t['file']) ) {
                    $tmp = array();
                    $func = !empty($t['class']) ? $t['class'].$t['type'].$t['function'].'()' : $t['function'];
                    $tmp[] = '<strong>at function '. $func.'</strong>';
                    $tmp[] = 'in file ' . $t['file'] . ', Line ' . $t['line'];
                    $line = implode('<br>', $tmp);
                    $filecontent = self::readFileLines($t['file'], $t['line'], $t['function']);
                    $line.= '<div class="filecontent">'.$filecontent.'</div>';
                } else {
                    $func = !empty($t['class']) ? $t['class'].$t['type'].$t['function'].'()' : $t['function'];
                    $line = '<strong>called from '. $func.'</strong>';
                }
                $stacktrace[] = $line;

            }

            if( !isset($filecontent) ) {
                $filecontent = self::readFileLines($error['file'], $error['line'], null);
                $stacktrace[] = 'in file ' . $error['file'] . ', Line ' . $error['line'];
                $line= '<div class="filecontent">'.$filecontent.'</div>';
                $stacktrace[] = $line;
            }

            $stacktrace = implode('<br>', $stacktrace);

            $html = '
			<html>
			<head><title>Exception</title>'.self::compressCss().'</head>
			<body>
			<div class="wrapper">
			<h3>Error on Server '.$_SERVER['SERVER_NAME'].' at port '.$_SERVER['SERVER_PORT'].'</h3>
			<div class="message">'.$error['message'].'</div>
			<h4>Stack Trace:</h4>
			<div>
			'.$stacktrace.'
			</div>
			</div>
			</body>
			</html>
			';

            self::output($html, $error['message']);
            exit;
        }
    }

    private static function readFileLines($file, $line, $func) {
        $from = $line >= 5 ? $line-4 : 0;
        $to = $line+3;
        $lines = array();

        // Fix eval'd code here
        $file = substr($file, 0, strpos($file, '.php')+4);

        if( !is_file($file) ) {
            //throw new \Exception("Unable to open file $file for reading.");
            print "Fatal Error: Unable to open file $file for reading.###";
            exit;
        }

        $fp = fopen($file, 'r');
        rewind($fp);
        $i = 0;
        while( !feof($fp) ) {
            $l = fgets($fp, 4096);
            $i++;
            $code = highlight_string('<?php '.$l.' ?>', true);
            $code = preg_replace('/\&lt;\?php([\&nbsp\;]+)/', '', $code);
            $code = preg_replace('/\?&gt;/', '', $code);
            $code = preg_replace('/\<br \/\>/', '', $code);
            $code = preg_replace('/\#FF8000/', '#999', $code);
            $lines[] = $code;//str_replace($func, '<strong><font color="#CD5C5C"><em>'.$func.'</em></font></strong>', htmlentities(trim($l), true));
        }
        fclose($fp);

        if( $to > $i )
            $to = $i;

        $result = array();
        for($i = $from; $i < $to; $i++ ) {
            $line_num = $line == $i+1 ? '<strong><font color="red">'.($i+1).'</font></strong>' : $i+1;
            $content = $line == $i+1 ? '<div style="padding: 2px;background-color: #F0F8FF;font-weight:800;">'.$lines[$i].'</div>' : '<span class="lineinfo">'.$lines[$i].'</span>';
            $result[] = '<tr><td class="fileline">'.$line_num.'</td><td class="line">'.$content.'</td></tr>';
        }

        return '<table cellpadding="0" cellspacing="0">'.implode('',$result).'</table>';
    }

    private static function output($html, $message) {
        $isAjax = false;

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isAjax = true;
        }

        if( $isAjax ) {
            header("Content-type: application/json");
            print json_encode(array('error' => true, 'message' => 'Internal Server Error<br>'.$message));
        } else {
        	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        	// TODO check for debug. If not, send error page
            (new Response($html, 500))->send();
        }
    }
    
    private static function compressCss() {
    	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', self::$css);
    	$buffer = str_replace(': ', ':', $buffer);
    	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    	return $buffer;
    }
}
