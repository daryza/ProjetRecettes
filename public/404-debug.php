<?php
http_response_code(404);
echo '404 Error: Requested URL - ' . $_SERVER['REQUEST_URI'];
