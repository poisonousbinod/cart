
<?php
session_start();
$page = 'index_one.php' ;

mysql_connect('localhost','root','') or die(mysql_error());
mysql_select_db('cart') or die(mysql_error());

if (isset($_GET['add'])) {
    $quantity = mysql_query('SELECT id, quantity FROM products WHERE id='.mysql_real_escape_string((int)$_GET['add']));
    while ($quantity_row = mysql_fetch_assoc($quantity)) {
        if ($quantity_row['quantity']!=$_SESSION['cart_'.(int)$_GET['add']]) {
            $_SESSION['cart_'.(int)$_GET['add']]+='1';
        }

    }
    header('Location: '.$page);

}

if (isset($_GET['remove'])) {
    $_SESSION['cart_'.(int)$_GET['remove']]--;
    header('Location: '.$page);
}
if (isset($_GET['delete'])) {
    $_SESSION['cart_'.(int)$_GET['delete']]='0';
    header('Location: '.$page);
}



function products() {
    $get = mysql_query('SELECT * FROM products WHERE quantity > 0 ORDER BY id DESC');
    if (mysql_num_rows($get)==0 ) {
    echo "There are no products to display.";
    }
    else
    {
    while ($get_row = mysql_fetch_assoc($get)) {
        echo '<p>'.$get_row['name'].'<br />'.$get_row['description'].'<br />&euro;'.number_format($get_row['price'] ,2).' <a href="cart.php?add='.$get_row['id'].'">Add</a></p>';
        }
    }

}

function paypal_items() {
    $num = 0;
    foreach($_SESSION as $name => $value) {
        if($value!=0) {
            if (substr($name, 0, 5)=='cart_') {
                $id = substr($name, 5, strlen($name)-5);
                $get = mysql_query('SELECT id, name, price FROM products WHERE id='.mysql_real_escape_string((int)$id));
                while ($get_row = mysql_fetch_assoc($get)) {
                $num++;
                echo '<input type="hidden" name="item_number_'.$num.'" value="'.$id.'">';
                echo '<input type="hidden" name="item_name_'.$num.'" value="'.$get_row['name'].'">';
                echo '<input type="hidden" name="amount_'.$num.'" value="'.$get_row['price'].'">';
                echo '<input type="hidden" name="shipping_'.$num.'" value="2.50">';
                echo '<input type="hidden" name="shipping2_'.$num.'" value="1.00">';
                echo '<input type="hidden" name="quantity_'.$num.'" value="'.$value.'">';
                }
            }
        }
    }
}

function cart() {
    foreach($_SESSION as $name => $value) {
    if ($value>0) {
        if (substr($name, 0, 5)=='cart_') {
            $id = substr($name, 5, (strlen($name)-5));
            $get = mysql_query('SELECT id, name, price FROM products WHERE id='.mysql_real_escape_string((int)$id));
            while ($get_row = mysql_fetch_assoc($get)) {
            $sub = $get_row['price']*$value;
                echo $get_row['name'].' x '.$value.' @ &euro;'.number_format($get_row['price'], 2).' = &euro;'.number_format($sub, 2).'<a href="cart.php?remove='.$id.'">[-]</a> <a href="cart.php?add='.$id.'">[+]</a> <a href="cart.php?delete='.$id.'">[Delete]</a><br />';
            }
        }
        $total += $sub;
    }
    }
        if ($total==0) {
            echo "Your cart is empty.";
        }
        else {
            echo '<p>Total: &euro;'.number_format($total, 2).'</p>';
            ?>
            <p>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_cart">
            <input type="hidden" name="upload" value="1">
            <input type="hidden" name="business" value="damienpbrick@hotmail.co.uk">
            <?php paypal_items(); ?>
            <input type="hidden" name="currency_code" value="EUR">
            <input type="hidden" name="amount" value="<?php echo $total; ?>">
            <input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but03.gif" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
            </form>
            </p>
            <?php
        }
}


?>