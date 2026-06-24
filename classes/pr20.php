<?php


class pr20
{
    public $dbs;

    function __construct($user, $pass, $host, $db)
    {
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        $charset = 'utf8';
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        try {
            $this->dbs = new PDO($dsn, $user, $pass, $opt);
            $GLOBALS['info'] = "Связь установлена";
        } catch (Exception $e) {
            $GLOBALS['info'] = "Связь не установлена";
        }
    }

    public function replaceParam($param, $type)
    {
        switch ($type) {
            case "atr":
                $param = substr($this->dbs->quote(trim($param)), 1, -1);
                break;
            case "search":
                $param = substr($this->dbs->quote("%" . trim($param) . "%"), 1, -1);
                break;
            case "md5":
                $param = substr($this->dbs->quote(md5(trim($param))), 1, -1);
                break;
        }
        return $param;
    }

    public function actionTable($action, $param, $table)
    {
        switch ($action) {
            case 'edit':
                $str = "UPDATE `" . $table . "` SET ";
                foreach ($param as $key => $value) {
                    if ($key != 'id') {
                        $str .= "`" . $key . "`='" . $value . "', ";
                    }
                }
                $str .= "WHERE id=" . $param['id'];
                $str = str_replace(", WHERE", " WHERE", $str);
                $r = $this->dbs->prepare($str);
                return ($r->execute());
            case 'del':
                $r = $this->dbs->prepare('DELETE FROM `' . $table . '` WHERE id=:i;');
                return ($r->execute([':i' => $this->replaceParam($param, "atr")]));
            case 'add':
                $str = "INSERT INTO `" . $table . "` SET ";
                foreach ($param as $key => $val) {
                    if (($key == 'pasw') || ($key == 'pass') || ($key == 'pas')  || ($key == 'passw') || ($key == 'password')) {
                        $str .= "`" . $this->replaceParam($key, "atr") . "`='" . $this->replaceParam($val, "md5") . "', ";
                    } else {
                        $str .= "`" . $this->replaceParam($key, "atr") . "`='" . $this->replaceParam($val, "atr") . "', ";
                    }
                }
                $str .= ";";
                $str = str_replace(", ;", ";", $str);
                $r = $this->dbs->prepare($str);
                return ($r->execute());
        }
        return false;
    }
    public function translit($str)
    {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ];
        return strtr($str, $converter);
    }
    public function uploading($input = 'files', $path = '/images/')
    {
        $mas=[];
// Название
        $input_name = $input;

// Разрешенные расширения файлов.
        $allow = [];
// Запрещенные расширения файлов.
        $deny = [
            'phtml', 'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'phps', 'cgi', 'pl', 'asp',
            'aspx', 'shtml', 'shtm', 'htaccess', 'htpasswd', 'ini', 'log', 'sh', 'js', 'html',
            'htm', 'css', 'sql', 'spl', 'scgi', 'fcgi'
        ];
// Директория куда будут загружаться файлы.
        $path = __DIR__. $path;
        if (isset($_FILES[$input_name])) {
            // Проверим директорию для загрузки.
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            // Преобразуем массив $_FILES в удобный вид для перебора в foreach.
            $files = [];
            $diff = count($_FILES[$input_name]) - count($_FILES[$input_name], COUNT_RECURSIVE);
            if ($diff == 0) {
                $files = [$_FILES[$input_name]];
            } else {
                foreach ($_FILES[$input_name] as $k => $l) {
                    foreach ($l as $i => $v) {
                        $files[$i][$k] = $v;
                    }
                }
            }
            /*print "<pre>";
            print_r($files);
            print "</pre>";*/
            $j=0;
            foreach ($files as $file) {
                $error = $success = '';
                // Проверим на ошибки загрузки.
                if (!empty($file['error']) || empty($file['tmp_name'])) {
                    switch (@$file['error']) {
                        case 1:
                        case 2:
                            $error = 'Превышен размер загружаемого файла.';
                            break;
                        case 3:
                            $error = 'Файл был получен только частично.';
                            break;
                        case 4:
                            $error = 'Файл не был загружен.';
                            break;
                        case 6:
                            $error = 'Файл не загружен - отсутствует временная директория.';
                            break;
                        case 7:
                            $error = 'Не удалось записать файл на диск.';
                            break;
                        case 8:
                            $error = 'PHP-расширение остановило загрузку файла.';
                            break;
                        case 9:
                            $error = 'Файл не был загружен - директория не существует.';
                            break;
                        case 10:
                            $error = 'Превышен максимально допустимый размер файла.';
                            break;
                        case 11:
                            $error = 'Данный тип файла запрещен.';
                            break;
                        case 12:
                            $error = 'Ошибка при копировании файла.';
                            break;
                        default:
                            $error = 'Файл не был загружен - неизвестная ошибка.';
                            break;
                    }
                } elseif ($file['tmp_name'] == 'none' || !is_uploaded_file($file['tmp_name'])) {
                    $error = 'Не удалось загрузить файл.';
                } else {
                    // Оставляем в имени файла только буквы, цифры и некоторые символы.
                    $pattern = "[^a-zа-яё0-9,~!@#%^-_\$\?\(\)\{\}\[\]\.]";
                    $name = mb_eregi_replace($pattern, '-', $file['name']);
                    $name = mb_ereg_replace('[-]+', '-', $name);
                }
                $this->translit($name);
                $parts = pathinfo($name);

                if (empty($name) || empty($parts['extension'])) {
                    $error = 'Недопустимое тип файла';
                } elseif (!empty($allow) && !in_array(strtolower($parts['extension']), $allow)) {
                    $error = 'Недопустимый тип файла';
                } elseif (!empty($deny) && in_array(strtolower($parts['extension']), $deny)) {
                    $error = 'Недопустимый тип файла';
                } else {
                    // Чтобы не затереть файл с таким же названием, добавим префикс.
                    $i = 0;
                    $prefix = '';
                    while (is_file($path . $parts['filename'] . $prefix . '.' . $parts['extension'])) {
                        $prefix = '(' . ++$i . ')';
                    }
                    $name = $parts['filename'] . $prefix . '.' . $parts['extension'];
                    // Перемещаем файл в директорию.
                    if (move_uploaded_file($file['tmp_name'], strtolower($path . $name))) {
                        // Далее можно сохранить название файла в БД и т.п.
                        $success = 'Файл «' . $name . '» успешно загружен.';
                        $mas[$j]="classes/images/".$name;
                        $j++;
                    } else {
                        $error = 'Не удалось загрузить файл.';
                    }
                }
            }
            // Выводим сообщение о результате загрузки.
            if (!empty($success)) {
                return $mas;
            } else {
                return $error;
            }
        }
    }

    public function message($text){
        $month_list = array(

            1  => 'января',

            2  => 'февраля',

            3  => 'марта',

            4  => 'апреля',

            5  => 'мая',

            6  => 'июня',

            7  => 'июля',

            8  => 'августа',

            9  => 'сентября',

            10 => 'октября',

            11 => 'ноября',

            12 => 'декабря'

        );
        $mes="<div class=\"toast show\" role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\">
        <div class=\"toast-header bg-primary text-white\">
            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-info-square-fill\" viewBox=\"0 0 16 16\">
                <path d=\"M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z\"/>
            </svg>
            <strong class=\"me-auto\">&nbsp;Сообщение</strong>
            <small>".date('d') . ' ' . $month_list[date('n')] . ' ' . date('Y')." ".date('H:i:s')."</small>
            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"toast\" aria-label=\"Закрыть\"></button>
        </div>
        <div class=\"toast-body\">
            ".$text."
        </div>
    </div>";
        return $mes;
    }
}