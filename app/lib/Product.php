<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 2019-12-05
 * Time: 01:30
 */

namespace backend;


use App\Ex_product;
use App\Ex_product_csv;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Self_;

class Product
{
    const MORECATORY=957;
    const STORE=0;
    private $product;
    private $description;
    private $stock;
    private $special;
    private $store;

    public function __construct(Ex_product $product)
    {
        $this->product = $product;
        $this->description = $product->description;
        $this->stock = $product->stock;
        $this->special = $product->special;
        $this->store = $product->store;
    }

    public static function find($product_id){
        $product = Ex_product::find($product_id);
        if ($product){
            return new self($product);
        }else{
            return null;
        }
    }

    public static function create(array $data){
        try{
            DB::beginTransaction();
            $product = Ex_product::create([
                'sku' => isset($data['sku'])?$data['sku']:'',
                'mpn' => isset($data['model'])?$data['model']:null,
                'quantity' => isset($data['quantity'])?$data['quantity']:0,
                'stock_status_id' => 9,
                'shipping' => 1,
                'price' => isset($data['price'])?round($data['price'],2):0,
                'tax_class_id' => 0,
                'weight' => isset($data['weight'])?$data['weight']:0,
                'weight_class_id' => 1,
                'length_class_id' => 1,
                'subtract' => 1,
                'sort_order' => 1,
                'status' => 1,
                'date_added' => Carbon::now()
            ]);
            $name = htmlspecialchars($data['name']);
            $product->description()->create([
                'language_id' => 1,
                'name'=> $name,
                'description' => $name,
                'meta_title' => $name
            ]);

            $product->stock()->create([
                'branch_akl'=>0,
                'warning_akl'=>0,
                'branch_wlg'=>0,
                'warning_wlg'=>0,
                'supplier'=>0
            ]);

            $product->store()->create([
                'store_id'=>self::STORE,
            ]);
            $product->categorys()->attach(self::MORECATORY);

            DB::commit();
            return new self($product);
        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }

    }

    public function updateMainImage($filePath){
        $image = file_get_contents($filePath);
        file_put_contents(config('filesystems.imageroute') . $this->product->sku . '.jpg', $image);
        $this->product->image = 'catalog/autoEx/' . $this->product->sku . '.jpg';
        $this->product->save();
        return $this;
    }

    public function updateSupplierStock($quantity){
        $this->stock->update([
            'supplier' => $quantity
        ]);
        return $this;
    }

    public function associateCsv(){
        if (!empty($this->product->mpn)){
            Ex_product_csv::where('product_id', -1)->where('model',$this->product->mpn)->update(['product_id'=>$this->product->product_id]);
        }
    }

    public function updateStock(){
        $this->stock->update(['supplier'=>$this->product->csvs()->sum('stock')]);
    }

    // to do add column in csv table called price_to_sell
    public function updatePrice(){
        $this->product->update([
            'price' => $this->product->csvs()->min('price')
        ]);
    }

    public function getProductId(){
        return $this->product->product_id;
    }

}