<?php

		require_once __DIR__."/../vendor/autoload.php";
		require_once __DIR__."/../src/Brand.php";
		require_once __DIR__."/../src/Store.php";

		$app = new Silex\Application();

		$app['debug'] = true;

		$server = 'mysql:host=localhost;dbname=shoes';
		$username = 'root';
		$password = 'root';
		$DB = new PDO($server, $username, $password);

		$app->register(new Silex\Provider\TwigServiceProvider(), array(
				'twig.path' => __DIR__.'/../views'
		));

		use Symfony\Component\HttpFoundation\Request;
		Request::enableHttpMethodParameterOverride();

	//Index Page
	$app->get("/", function() use ($app){
		return $app['twig']->render('index.html.twig');
	});

	$app->get('/stores', function() use ($app){
		return $app['twig']->render('stores.html.twig', array('stores'=> Store::getAll()));
	});

	$app->post('/stores', function() use ($app){
		$new_store = new Store($_POST['new_store']);
		$new_store->save();
		return $app['twig']->render("stores.html.twig", array('stores'=> Store::getAll()));
	});

	$app->post('/stores/delete', function() use ($app){
		Store::deleteAll();
		return $app['twig']->render("stores.html.twig", array('stores'=> Store::getAll()));
	});
	$app->get('/stores/{id}', function($id) use ($app){
		$store = Store::find($id);
		return $app['twig']->render("store.html.twig", array('store'=>$store, 'stores'=> Store::getAll(), 'brands'=> $store->getBrands(), 'all_brands'=> Brand::getAll()));
	});
	$app->patch('/updateStore/{id}', function($id) use ($app){
		$store = Store::find($id);
		$store->update($_POST['update_store']);
		return $app['twig']->render("index.html.twig", array('stores'=> Store::getAll()));
	});
	$app->delete('/deleteStore/{id}', function($id) use ($app){
		$store = Store::find($id);
		$store->delete();
		return $app['twig']->render("stores.html.twig". array('stores'=> Store::getAll()));
	});
	$app->get("/brands", function() use ($app){
		return $app['twig']->render("brands.html.twig", array('brands'=>Brand::getAll()));
	});
	$app->post('/brands', function() use($app) {
		$new_brand = new Brand($_POST['brand_name']);
        $new_brand->save();
        return $app['twig']->render('brands.html.twig', array ('brands' => Brand::getAll()));
    });
	$app->post('/brands/delete', function() use ($app) {
        Brand::deleteAll();
        return $app['twig']->render('brands.html.twig', array('brands' => Brand::deleteAll()));
    });

    $app->get('/brand/{id}', function($id) use ($app) {
        $brand = Brand::find($id);
        return $app['twig']->render('brand.html.twig', array('brand' => $brand, 'stores' => Store::getAll(), 'stores' => $brand->getStores(), 'all_stores' =>Store::getAll()));
    });
    $app->post('/brand/addStores', function() use ($app) {
        $brand = Brand::find($_POST['brand_id']);
        $store = Store::find($_POST['store_id']);
        $brand->addStore($store);
				$stores = $brand->getStores();
				$all_stores = Store::getAll();
				return $app['twig']->render('brands.html.twig', array('brands' => Brand::getAll(), 'stores' => $stores, 'all_stores' => $all_stores));
     });

		$app->post('/addStores', function() use($app) {
					$new_store = new Store($_POST['store_id']);
					$new_store->save();
					return $app['twig']->render('stores.html.twig', array ('stores' => Store::getAll()));
		});


   $app->delete('/brand/{id}', function($id) use ($app) {
         $brand = Brand::find($id);
         $brand->delete();
         return $app['twig']->render('brands.html.twig', array('brands' => Brand::getAll()));
    });

		$app->post('/addBrands', function() use ($app) {
				 $store = Store::find($_POST['store_id']);
				 $brand = Brand::find($_POST['brand_id']);
				 $store->addBrand($brand);
				 $brands = $store->getBrands();
				 $all_brands = Brand::getAll();
				 return $app['twig']->render('stores.html.twig', array('stores' => Store::getAll(), 'brands' => $brands, 'all_brands' => $all_brands));
		});

		return $app;
?>
