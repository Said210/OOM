<?
	require "PHPrecord.php";
	
	// $i = PHPrecord::factory_f("inventory");
	// $inner = PHPrecord::factory_f("inventory");

	// $qi = $inner->select("sum(transaction) as amount, product")->group("product")->order("date")->query;

	// $i->select()->from("($qi) as holder");
	// $i->join("products", "holder.product")->join("brands", "products.brand");
	// $r = $i->run(false);
	// var_export($r);

	$i = PHPrecord::factory_f("mocla");
	$i->attr["diseno_id"] = 1;
	$i->attr["capacity"] = 900;
	$i->attr["user_id"] = 2;
	var_dump($i);
	var_export($i->save());

	/*
SELECT 
	products.id,
	products.name,
	products.image,
	products.price,
	brands.description as brand,
    amount
FROM (
	SELECT
		sum(transaction) as amount,
        product
        FROM inventory
        group by product
        order by date asc
	) as holder
join products on holder.product = products.id
join brands on products.brand = brands.id
where products.visible = 1
*/
?>



