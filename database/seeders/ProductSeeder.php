<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = Brand::all();
        $menCategory = Category::where('name', 'Nam')->first();
        $womenCategory = Category::where('name', 'Nữ')->first();
        $kidsCategory = Category::where('name', 'Trẻ em')->first();
        
        $menSubcategories = Category::where('parent_id', $menCategory->id)->get();
        $womenSubcategories = Category::where('parent_id', $womenCategory->id)->get();
        $kidsSubcategories = Category::where('parent_id', $kidsCategory->id)->get();

        // Sample products for Men
        $menProducts = [
            [
                'name' => 'Air Max 90',
                'description' => 'Giày Nike Air Max 90 trung thành với thiết kế gốc từ dòng giày chạy bộ với đế ngoài Waffle đặc trưng, các lớp phủ may và điểm nhấn TPU cổ điển.',
                'price' => 3199000,
                'sale_price' => 2799000,
                'features' => 'Đế giữa bằng foam; Đế ngoài cao su kiểu Waffle; Dây buộc điều chỉnh; Cổ giày thấp có đệm',
                'brand_id' => $brands->where('name', 'Nike')->first()->id,
                'featured' => true,
                'categories' => [$menSubcategories->where('name', 'Giày sneaker thời trang')->first()->id],
            ],
            [
                'name' => 'Ultra Boost 21',
                'description' => 'Phát triển với mỗi bước chạy trong đôi giày Adidas Ultra Boost 21. Đôi giày chạy bộ nam này mang lại năng lượng và sự thoải mái tuyệt vời.',
                'price' => 4300000,
                'sale_price' => null,
                'features' => 'Thân giày Primeknit; Đế giữa Boost; Đế ngoài Continental™ Rubber; Hệ thống Torsion',
                'brand_id' => $brands->where('name', 'Adidas')->first()->id,
                'featured' => true,
                'categories' => [$menSubcategories->where('name', 'Giày chạy bộ')->first()->id],
            ],
            [
                'name' => 'Suede Classic XXI',
                'description' => 'Giày Puma Suede Classic XXI nâng tầm phom dáng mang tính biểu tượng với chất liệu cao cấp và sự thoải mái hiện đại.',
                'price' => 1700000,
                'sale_price' => 1499000,
                'features' => 'Thân giày bằng da lộn; Đế ngoài cao su; Thiết kế Formstrip đặc trưng; Cổ giày có đệm',
                'brand_id' => $brands->where('name', 'Puma')->first()->id,
                'featured' => false,
                'categories' => [$menSubcategories->where('name', 'Giày sneaker thời trang')->first()->id],
            ],
            [
                'name' => 'LeBron 19',
                'description' => 'Nike LeBron 19 tận dụng tốc độ và sức mạnh phi thường của LeBron với đệm hồi phản và các yếu tố thiết kế hỗ trợ.',
                'price' => 5000000,
                'sale_price' => null,
                'features' => 'Các đơn vị Nike Air Zoom; Thân giày kỹ thuật; Đế ngoài cao su bền; Bộ phận gót TPU đúc',
                'brand_id' => $brands->where('name', 'Nike')->first()->id,
                'featured' => true,
                'categories' => [$menSubcategories->where('name', 'Giày bóng rổ')->first()->id],
            ],
            [
                'name' => '574 Classic',
                'description' => '574 Classic là đôi giày phong cách sống sạch sẽ và cổ điển từ New Balance, có thân giày bằng da lộn và lưới phù hợp cho việc mặc hàng ngày.',
                'price' => 2100000,
                'sale_price' => 1850000,
                'features' => 'Đế giữa ENCAP; Đệm xốp EVA; Thân giày da lộn/lưới; Đế ngoài cao su',
                'brand_id' => $brands->where('name', 'New Balance')->first()->id,
                'featured' => false,
                'categories' => [$menSubcategories->where('name', 'Giày sneaker thời trang')->first()->id],
            ]
        ];

        // Sample products for Women
        $womenProducts = [
            [
                'name' => 'React Infinity Run Flyknit',
                'description' => 'Nike React Infinity Run Flyknit được thiết kế để giúp giảm chấn thương và giữ cho bạn luôn có thể chạy.',
                'price' => 3800000,
                'sale_price' => 3300000,
                'features' => 'Đế mút Nike React; Thân giày Flyknit; Phần bàn chân trước rộng hơn; Đế có hình dạng lắc lư',
                'brand_id' => $brands->where('name', 'Nike')->first()->id,
                'featured' => true,
                'categories' => [$womenSubcategories->where('name', 'Giày chạy bộ')->first()->id],
            ],
            [
                'name' => 'Chuck Taylor All Star',
                'description' => 'Giày sneaker cao cổ Converse Chuck Taylor All Star bất hủ với thân vải canvas và thiết kế kinh điển.',
                'price' => 1500000,
                'sale_price' => null,
                'features' => 'Thân giày vải canvas; Đế ngoài cao su; Lỗ xỏ dây phía trong; Lót giày OrthoLite',
                'brand_id' => $brands->where('name', 'Converse')->first()->id,
                'featured' => false,
                'categories' => [$womenSubcategories->where('name', 'Giày sneaker thời trang')->first()->id],
            ],
            [
                'name' => 'Old Skool',
                'description' => 'Vans Old Skool là đôi giày trượt ván cổ điển và là đôi giày đầu tiên có sọc bên hông đặc trưng của Vans.',
                'price' => 1550000,
                'sale_price' => null,
                'features' => 'Thân giày da lộn và canvas; Kết cấu vulcanized; Đế ngoài đặc trưng kiểu bánh quế',
                'brand_id' => $brands->where('name', 'Vans')->first()->id,
                'featured' => true,
                'categories' => [$womenSubcategories->where('name', 'Giày sneaker thời trang')->first()->id],
            ],
            [
                'name' => 'Nano X2 Training',
                'description' => 'Reebok Nano X2 được thiết kế cho những người tập luyện chăm chỉ với hỗ trợ gót chân nâng cao và độ linh hoạt ở bàn chân trước.',
                'price' => 3300000,
                'sale_price' => 2850000,
                'features' => 'Thân giày dệt Flexweave; Xốp Floatride Energy; Kẹp gót chân để ổn định',
                'brand_id' => $brands->where('name', 'Reebok')->first()->id,
                'featured' => false,
                'categories' => [$womenSubcategories->where('name', 'Giày tập luyện')->first()->id],
            ]
        ];

        // Sample products for Kids
        $kidsProducts = [
            [
                'name' => 'Air Force 1 Kids',
                'description' => 'Thiết kế Air Force 1 huyền thoại, được làm nhỏ lại cho trẻ em với thân giày bằng da bền và đế giữa có đệm.',
                'price' => 2100000,
                'sale_price' => 1850000,
                'features' => 'Thân giày bằng da; Đệm Air; Đế ngoài cao su không để lại dấu',
                'brand_id' => $brands->where('name', 'Nike')->first()->id,
                'featured' => true,
                'categories' => [$kidsSubcategories->where('name', 'Giày bé trai')->first()->id],
            ],
            [
                'name' => 'Superstar Kids',
                'description' => 'Adidas Superstar cổ điển với mũi giày vỏ sò mang tính biểu tượng, được làm nhỏ lại cho trẻ em với khóa dễ sử dụng.',
                'price' => 1700000,
                'sale_price' => null,
                'features' => 'Thân giày bằng da; Mũi giày vỏ sò bằng cao su; Khóa dây điều chỉnh',
                'brand_id' => $brands->where('name', 'Adidas')->first()->id,
                'featured' => false,
                'categories' => [$kidsSubcategories->where('name', 'Giày bé gái')->first()->id],
            ],
            [
                'name' => 'Tiny Slip-On',
                'description' => 'Vans Tiny Slip-On là đôi giày dễ mang dành cho trẻ mới biết đi với kiểu dáng và cảm giác cổ điển của Vans.',
                'price' => 950000,
                'sale_price' => 850000,
                'features' => 'Thân giày bằng canvas; Điểm nhấn đàn hồi ở hai bên; Cổ giày có đệm; Đế ngoài đặc trưng kiểu bánh quế',
                'brand_id' => $brands->where('name', 'Vans')->first()->id,
                'featured' => true,
                'categories' => [$kidsSubcategories->where('name', 'Giày trẻ mới biết đi')->first()->id],
            ]
        ];

        // Create all products and attach categories
        $allProducts = array_merge($menProducts, $womenProducts, $kidsProducts);
        
        foreach ($allProducts as $productData) {
            $categories = $productData['categories'];
            unset($productData['categories']);
            
            $product = Product::create(array_merge($productData, [
                'slug' => Str::slug($productData['name']),
            ]));
            
            $product->categories()->attach($categories);
        }
    }
}