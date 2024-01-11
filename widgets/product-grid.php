<?php
namespace Phytoceutics\ElementorWidgets\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WC_Product_Simple;

class Product_Grid extends Widget_Base
{

    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);

        wp_enqueue_style("phyto-product-grid-css", plugin_dir_url(__FILE__) . "../assets/css/product-grid.css");
        wp_enqueue_script("phyto-product-grid-js", plugin_dir_url(__FILE__) . "../assets/js/product-grid.js");
    }
    public function get_name()
    {
        return "phytoceutics-product-grid";
    }

    public function get_style_depends()
    {
        return ['phyto-product-grid-css'];
    }

    public function get_script_depends()
    {
        return ['phyto-product-grid-js'];
    }

    public function get_title()
    {
        return __("Phytoceutics Product Grid", "phytoceutics-elementor-widgets");
    }

    public function get_icon()
    {
        return "eicon-products";
    }

    public function get_categories()
    {
        return ["basic"];
    }

    public function _register_controls()
    {
        $this->start_controls_section('content', [
            'label' => esc_html__('Content', 'phytoceutics-elementor-widgets'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control(
            'title',
            [
                'type' => Controls_Manager::TEXT,
                'default' => __('Products', 'phytoceutics-elementor-widgets'),
                'label' => esc_html__('Title', 'phytoceutics-elementor-widgets'),
                'placeholder' => esc_html__('Enter your title', 'phytoceutics-elementor-widgets'),
            ]
        );


        $this->add_control(
            'columns',
            [
                'type' => Controls_Manager::SELECT,
                'default' => 3,
                'label' => esc_html__('Columns', 'phytoceutics-elementor-widgets'),
                'options' => [
                    3 => esc_html__('3', 'phytoceutics-elementor-widgets'),
                    2 => esc_html__('2', 'phytoceutics-elementor-widgets'),

                ],
            ]
        );

        $this->end_controls_section();


    }

    protected function render()
    {
        // $settings = $this->get_settings_for_display();
        // $columns = $settings['columns'];
        $products = $this->get_products([$_GET['condition'], $_GET['brand']]);
        $categories = $this->get_product_categories();
        ?>
        <div class="phyto-grid-container">
            <div class="sidebar">
                <form id="filter-form">
                    <?php
                    foreach ($categories as $name => $category) {
                        $this->render_category_options($category, $name);
                    }
                    ?>
                    <button type="submit" class="phyto-btn">Apply Filters</button>
                </form>
            </div>
            <div class="phyto-product-grid">
                <?php
                foreach ($products as $product) {
                    $this->render_product_card($product);
                }
                ?>
            </div>
        </div>
        <?php
    }

    protected function render_category_options($category, $name)
    {
        $current_value = $_GET[strtolower($name)];
        ?>
        <div class="form-group">
            <div class="form-label">Filter By
                <?= $name ?>
            </div>
            <select name="<?= strtolower($name) ?>" id="<?= strtolower($name) ?>" class="form-control">
                <option value="">Select
                    <?= $name ?>
                </option>
                <?php
                foreach ($category as $key => $value) {
                    $selected = ($current_value == strtolower($value->name)) ? 'selected="selected"' : '';

                    ?>
                    <option <?= $selected ?> value="<?= strtolower($value->name) ?>">
                        <?= $value->name ?>
                    </option>

                    <?php
                }
                ?>
            </select>
        </div>
        <?php
    }

    protected function get_products(array $categories)
    {
        return wc_get_products([
            'posts_per_page' => 10,
            'category' => json_encode(array_filter($categories, function ($category) {
                return strlen($category) > 0;
            }))
        ]);
    }

    protected function get_product_categories()
    {
        $taxonomy = 'product_cat';
        $orderby = 'name';
        $show_count = 0;
        $pad_counts = 0;
        $hierarchical = 1;
        $title = '';
        $empty = 0;

        $all_categories = get_categories([
            'taxonomy' => $taxonomy,
            'orderby' => $orderby,
            'show_count' => $show_count,
            'pad_counts' => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li' => $title,
            'hide_empty' => $empty
        ]);


        $subcategories = array_filter($all_categories, function ($category) {
            return $category->name != 'Uncategorized';
        });


        $category_lookup = array_reduce(
            $subcategories,
            function ($carry, $item) {
                $carry[$item->term_id] = $item->name;
                return $carry;
            },
            []
        );




        return array_reduce($subcategories, function ($result, $item) use ($category_lookup) {
            if ($item->parent == 0) {
                if (!isset($result[$item->name])) {
                    $result[$item->name] = [];
                }
            } else {
                $result[$category_lookup[$item->parent]][] = $item;
            }
            return $result;
        }, []);
    }

    protected function render_product_card(WC_Product_Simple $product)
    {
        ?>
        <div class="phyto-grid-item">
            <a href="#" class="thumbnail">
                <div class="thumbnail-overlay"></div>
                <?= $product->get_image() ?>
            </a>
            <a class="title" href="#">
                <?= $product->get_name() ?>
            </a>
            <div class="price">
                <?= $product->get_price_html() ?>
            </div>
        </div>
        <?php
    }
}