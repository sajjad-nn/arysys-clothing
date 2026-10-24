```vue
<script setup>
const products = [
    {
        id: 1,
        name: 'هودی اسپرت مردانه',
        image: 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=500',
        oldPrice: '1,850,000',
        price: '1,290,000',
        discount: 30
    },
    {
        id: 2,
        name: 'کت جین زنانه',
        image: 'https://images.unsplash.com/photo-1543076447-215ad9ba6923?w=500',
        oldPrice: '2,400,000',
        price: '1,680,000',
        discount: 30
    },
    {
        id: 3,
        name: 'شلوار کتان مردانه',
        image: 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=500',
        oldPrice: '1,600,000',
        price: '1,120,000',
        discount: 30
    },
    {
        id: 4,
        name: 'پیراهن زنانه',
        image: 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=500',
        oldPrice: '2,100,000',
        price: '1,470,000',
        discount: 30
    },
    {
        id: 5,
        name: 'کت اسپرت مردانه',
        image: 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=500',
        oldPrice: '3,200,000',
        price: '2,240,000',
        discount: 30
    }
]
</script>

<template>
    <section class="  special-offers">

        <div class="offers-header">
            <div class="title-wrapper">
                <span class="offer-icon">%</span>

                <div>
                    <h2>فروش ویژه</h2>
                    <p>پیشنهادهای ویژه برای شما</p>
                </div>
            </div>

            <a href="#" class="view-all">
                مشاهده همه
                <span>←</span>
            </a>
        </div>

        <div class="products-wrapper">

            <article
                v-for="product in products"
                :key="product.id"
                class="product-card"
            >

                <div class="product-image">

                    <span class="discount">
                        {{ product.discount }}٪
                    </span>

                    <img
                        :src="product.image"
                        :alt="product.name"
                    >

                </div>

                <div class="product-info">

                    <h3>{{ product.name }}</h3>

                    <div class="price-row">

                        <div class="price">
                            <strong>{{ product.price }}</strong>
                            <span>تومان</span>
                        </div>

                        <span class="old-price">
                            {{ product.oldPrice }}
                        </span>

                    </div>

                </div>

            </article>

        </div>

    </section>
</template>

<style scoped>
.special-offers {
    width: 60%;
    margin: 50px auto 0;
    padding: 25px;
    box-sizing: border-box;
    background: #ef4444;
    border-radius: 20px;
    direction: rtl;
}

/* Header */

.offers-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 20px;
}

.title-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;

    color: white;
}

.offer-icon {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 45px;
    height: 45px;

    border-radius: 50%;

    background: white;
    color: #ef4444;

    font-size: 22px;
    font-weight: 800;
}

.title-wrapper h2 {
    margin: 0;

    font-size: 24px;
    font-weight: 800;
}

.title-wrapper p {
    margin: 4px 0 0;

    color: rgba(255, 255, 255, 0.8);
    font-size: 13px;
}

.view-all {
    display: flex;
    align-items: center;
    gap: 8px;

    color: white;
    text-decoration: none;

    font-size: 14px;
    font-weight: 600;
}

.view-all span {
    font-size: 18px;
}

/* Products */

.products-wrapper {
    display: grid;
    grid-template-columns: repeat(5, 1fr);

    gap: 10px;

    overflow-x: auto;
}

/* Card */

.product-card {
    min-width: 0;

    padding: 12px;

    background: white;
    border-radius: 14px;

    overflow: hidden;
}

/* Image */

.product-image {
    position: relative;

    width: 100%;
    height: 190px;

    overflow: hidden;
    border-radius: 10px;

    background: #f3f4f6;
}

.product-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.discount {
    position: absolute;
    top: 8px;
    right: 8px;

    z-index: 2;

    padding: 4px 8px;

    border-radius: 6px;

    background: #ef4444;
    color: white;

    font-size: 12px;
    font-weight: 700;
}

/* Info */

.product-info {
    padding: 12px 3px 3px;
}

.product-info h3 {
    height: 42px;

    margin: 0 0 12px;

    color: #374151;

    font-size: 14px;
    font-weight: 600;
    line-height: 1.6;

    overflow: hidden;
}

/* Price */

.price-row {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.price {
    display: flex;
    align-items: center;
    gap: 5px;

    color: #111827;
}

.price strong {
    font-size: 15px;
}

.price span {
    font-size: 11px;
    color: #6b7280;
}

.old-price {
    color: #9ca3af;

    font-size: 12px;
    text-decoration: line-through;
}

/* Responsive */

@media (max-width: 1100px) {
    .products-wrapper {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 850px) {
    .products-wrapper {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 650px) {
    .special-offers {
        padding: 18px;
    }

    .offers-header {
        margin-bottom: 15px;
    }

    .title-wrapper h2 {
        font-size: 20px;
    }

    .products-wrapper {
        grid-template-columns: repeat(2, 1fr);
    }

    .product-image {
        height: 170px;
    }
}

@media (max-width: 450px) {
    .products-wrapper {
        grid-template-columns: repeat(2, 180px);
    }

    .product-image {
        height: 160px;
    }

    .view-all {
        font-size: 12px;
    }
}
</style>
