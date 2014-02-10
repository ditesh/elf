<?php

$path = "/home/ditesh/squid/users";
$ini = parse_ini_file("/home/ditesh/squid/groups.ini", TRUE);
$cached = array();
$cached["domains_files"] = array();
date_default_timezone_set("Asia/Kuala_Lumpur");

while(1) {

        $loopflag = FALSE;
        $timeout = 3*60;

        $metadata = stream_get_meta_data(STDIN);
        if ($metadata["eof"] === TRUE) exit;

        $str = fgets(STDIN);
        $str = explode(" ", $str);
        $username = trim(rawurldecode($str[0]));
        $website = trim(rawurldecode($str[1]));

        if (strlen($username) === 0) continue;

        file_put_contents("/tmp/santa", "'".json_encode(array($username, $website))."'\n", FILE_APPEND);

        foreach($ini as $group=>$data) {

                $users = explode(",", $data["users"]);
                if (in_array($username, $users) === TRUE) {

                        if ($data["timer"] === "enabled") {

                                if (strlen($data["timeout"]) > 0) $timeout = intval($data["timeout"]);

                                if (file_exists("$path/$username")) {

                                        $timestamp = filectime("$path/$username");

                                        if (date("d-m-Y") === date("d-m-Y", $timestamp)) {

                                                if (time() - $timestamp > $timeout) {

                                                        file_put_contents("/tmp/santa", "'$username errs because timeout $timeout'\n", FILE_APPEND);
                                                        $loopflag = TRUE;
                                                        echo "ERR\n";
                                                        break;

                                                }

                                        } else {

                                                unlink("$path/$username");
                                                touch("$path/$username");

                                        }
                                } else {

                                        touch("$path/$username");

                                }
                        }

                        if (strlen($data["disallowed_domains"]) > 0) {

                                if ($data["disallowed_domains"] === "all") {

                                        if (strlen($data["allowed_domains"]) > 0) {

                                                $domains = explode(",", $data["allowed_domains"]);

                                                if (!in_array($website, $domains)) {

                                                        $loopflag = TRUE;
                                                        echo "ERR\n";
                                                        break;

                                                }
                                        } else {

                                                $loopflag = TRUE;
                                                echo "ERR\n";
                                                break;

                                        }
                                } else {

                                    $domains = explode(",", $data["disallowed_domains"]);

                                    if (in_array($website, $domains)) {

                                        $loopflag = TRUE;
                                        echo "ERR\n";
                                        break;

                                    }
                                }
                        }

                        if (strlen($data["disallowed_domains_files"]) > 0) {

                                if ($cached["domains_files"][$group] === NULL) {

                                    $files = explode(",", $data["disallowed_domains_files"]);

                                    foreach($files as $file) {

                                        if (file_exists($file)) {

                                            $cached["domains_files"][$group][] = file_get_contents($file);

                                        }
                                    }
                                }

                                if (in_array($website, $cached["domains_files"][$group])) {

                                    $loopflag = TRUE;
                                    echo "ERR\n";
                                    break;

                                }
                        }
                }
        }

        if ($loopflag === FALSE) echo "OK\n";

}

?>
