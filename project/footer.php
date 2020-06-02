<?php
include_once 'config.php';

/**
 * @file
 * Footer class file.
 */

/**
 * Class Footer
 *
 * @see Header
 */
class Footer
{
    /**
     *
     * Inserting overall footer HTML code into page
     *
     */
    function insert_footer()
    {
        echo '
    <footer>
        <hr>
        <div>Moja strona 2020</div>
    </footer>
</body>
</html>';
    }
}