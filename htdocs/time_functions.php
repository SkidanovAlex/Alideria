<?php
                                                      
$fp = fopen(dirname(__FILE__) . '/log.txt', 'a+');
fwrite($fp , $argv[0] . PHP_EOL);
fclose($fp);
                                                    
