<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            if (!$this->indexExists('products', 'products_vendor_id_index')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->index('vendor_id', 'products_vendor_id_index');
                });
            }

            if (!$this->indexExists('products', 'products_name_index')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->index('name', 'products_name_index');
                });
            }
        }

        if (Schema::hasTable('orders')) {
            if (!$this->indexExists('orders', 'orders_vendor_id_index')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->index('vendor_id', 'orders_vendor_id_index');
                });
            }

            if (!$this->indexExists('orders', 'orders_status_index')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->index('status', 'orders_status_index');
                });
            }

            if (!$this->indexExists('orders', 'orders_created_at_index')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->index('created_at', 'orders_created_at_index');
                });
            }
        }

        if (Schema::hasTable('order_items')) {
            if (!$this->indexExists('order_items', 'order_items_order_id_index')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->index('order_id', 'order_items_order_id_index');
                });
            }

            if (!$this->indexExists('order_items', 'order_items_product_id_index')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->index('product_id', 'order_items_product_id_index');
                });
            }
        }

        if (Schema::hasTable('vendors')) {
            if (!$this->columnIndexed('vendors', 'email')) {
                Schema::table('vendors', function (Blueprint $table) {
                    $table->index('email', 'vendors_email_index');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            if ($this->indexExists('products', 'products_vendor_id_index')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropIndex('products_vendor_id_index');
                });
            }

            if ($this->indexExists('products', 'products_name_index')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropIndex('products_name_index');
                });
            }
        }

        if (Schema::hasTable('orders')) {
            if ($this->indexExists('orders', 'orders_vendor_id_index')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->dropIndex('orders_vendor_id_index');
                });
            }

            if ($this->indexExists('orders', 'orders_status_index')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->dropIndex('orders_status_index');
                });
            }

            if ($this->indexExists('orders', 'orders_created_at_index')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->dropIndex('orders_created_at_index');
                });
            }
        }

        if (Schema::hasTable('order_items')) {
            if ($this->indexExists('order_items', 'order_items_order_id_index')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->dropIndex('order_items_order_id_index');
                });
            }

            if ($this->indexExists('order_items', 'order_items_product_id_index')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->dropIndex('order_items_product_id_index');
                });
            }
        }

        if (Schema::hasTable('vendors')) {
            if ($this->indexExists('vendors', 'vendors_email_index')) {
                Schema::table('vendors', function (Blueprint $table) {
                    $table->dropIndex('vendors_email_index');
                });
            }
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $rows = DB::select('SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$index]);

        return count($rows) > 0;
    }

    private function columnIndexed(string $table, string $column): bool
    {
        $rows = DB::select('SHOW INDEX FROM `' . $table . '` WHERE Column_name = ?', [$column]);

        return count($rows) > 0;
    }
};
