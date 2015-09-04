<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/8/2015
 * Time: 11:39 AM
 */

/**
 * SCSS server
 *
 * @author Leaf Corcoran <leafot@gmail.com>
 */
class ScssServer {
    /**
     * Join path components
     *
     * @param string $left  Path component, left of the directory separator
     * @param string $right Path component, right of the directory separator
     *
     * @return string
     */
    protected function join($left, $right) {
        return rtrim($left, '/\\') . DIRECTORY_SEPARATOR . ltrim($right, '/\\');
    }

    /**
     * Get name of requested .scss file
     *
     * @return string|null
     */
    protected function inputName() {
        switch (true) {
            case isset($_GET['p']):
                return $_GET['p'];
            case isset($_SERVER['PATH_INFO']):
                return $_SERVER['PATH_INFO'];
            case isset($_SERVER['DOCUMENT_URI']):
                return substr($_SERVER['DOCUMENT_URI'], strlen($_SERVER['SCRIPT_NAME']));
            default:
                return null;
        }
    }

    /**
     * Get path to requested .scss file
     *
     * @return string
     * @throws FwException
     */
    protected function findInput() {
        if(is_file($this->dir.'/main.scss')) {
            return $this->dir.'/main.scss';
        } else {
            throw new FwException("Can't find '".$this->dir."/main.scss' file.");
        }
    }

    /**
     * Get path to cached .css file
     * @param $fname string file name
     * @return string
     */
    protected function cacheName($fname) {
        return $this->join($this->cacheDir, md5($fname) . '.css');
    }

    /**
     * Get path to cached imports
     * @param $out string
     * @return string
     */
    protected function importsCacheName($out) {
        return $out . '.imports';
    }

    /**
     * Determine whether .scss file needs to be re-compiled.
     *
     * @param string $in  Input path
     * @param string $out Output path
     *
     * @return boolean True if compile required.
     */
    protected function needsCompile($in, $out) {
        if (!is_file($out)) return true;

        $mtime = filemtime($out);
        if (filemtime($in) > $mtime) return true;

        // look for modified imports
        $icache = $this->importsCacheName($out);
        if (is_readable($icache)) {
            $imports = unserialize(file_get_contents($icache));
            foreach ($imports as $import) {
                if (filemtime($import) > $mtime) return true;
            }
        }
        return false;
    }

    /**
     * Get If-Modified-Since header from client request
     *
     * @return string
     */
    protected function getModifiedSinceHeader()
    {
        $modifiedSince = '';

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $modifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'];

            if (false !== ($semicolonPos = strpos($modifiedSince, ';'))) {
                $modifiedSince = substr($modifiedSince, 0, $semicolonPos);
            }
        }

        return $modifiedSince;
    }

    /**
     * Compile .scss file
     *
     * @param string $in  Input path (.scss)
     * @param string $out Output path (.css)
     *
     * @return string
     */
    protected function compile($in, $out) {
        $start = microtime(true);
        $css = $this->scss->compile(file_get_contents($in), $in);
        $elapsed = round((microtime(true) - $start), 4);

        $v = ScssCompiler::$VERSION;
        $t = @date('r');
        $css = "/* compiled by scssphp $v on $t (${elapsed}s) */\n\n" . $css;

        file_put_contents($out, $css);

        file_put_contents($this->importsCacheName($out),
            serialize($this->scss->getParsedFiles()));
        return $css;
    }

    /**
     * Compile requested scss and serve css.  Outputs HTTP response.
     *
     * @param string $salt Prefix a string to the filename for creating the cache name hash
     */
    public function serve($salt = '') {
        $protocol = isset($_SERVER['SERVER_PROTOCOL'])
            ? $_SERVER['SERVER_PROTOCOL']
            : 'HTTP/1.0';

        if ($input = $this->findInput()) {
            $output = $this->cacheName($salt . $input);
            if (true || $this->needsCompile($input, $output)) {
                try {
                    $css = $this->compile($input, $output);
                    if(!is_dir('css')){
                        mkdir('css', 0777);
                    }
                    file_put_contents('css/compilled.css', $css);
                    return;
                } catch (Exception $e) {
                    header($protocol . ' 500 Internal Server Error');
                    header('Content-type: text/plain');

                    echo 'Parse error: ' . $e->getMessage() . "\n";
                    exit;
                }
            }
            return;
        }

        header($protocol . ' 404 Not Found');
        header('Content-type: text/plain');

        $v = ScssCompiler::$VERSION;
        echo "/* INPUT NOT FOUND scss $v */\n";
    }

    /**
     * Constructor
     *
     * @param string      $dir      Root directory to .scss files
     * @param string      $cacheDir Cache directory
     * @param \ScssCompiler|null $scss     SCSS compiler instance
     */
    public function __construct($dir, $cacheDir=null, $scss=null) {
        $this->dir = $dir;

        if (!isset($cacheDir)) {
            $cacheDir = $this->join($dir, 'scss_cache');
        }

        $this->cacheDir = $cacheDir;
        if (!is_dir($this->cacheDir)) mkdir($this->cacheDir, 0755, true);

        if (!isset($scss)) {
            $scss = new ScssCompiler();
            $scss->setImportPaths($this->dir);
        }
        $this->scss = $scss;
    }

    /**
     * Helper method to serve compiled scss
     *
     * @param string $path Root path
     */
    static public function serveFrom($path) {
        $server = new self($path);
        $server->serve();
    }
}
