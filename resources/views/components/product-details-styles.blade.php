@once
    <style>
        /* GHS product details – tab colours
           default: green | hover: pink | active: pink text + light pink box */
        .tabs-listing .product-tabs li a {
            color: #3f5b3a;
            padding: 8px 14px;
            margin-right: 8px;
            border-radius: 8px;
            transition: color .2s ease, background-color .2s ease;
        }

        /* Replace the sliding underline with the light-pink box on active. */
        .tabs-listing .product-tabs li a:before {
            display: none;
        }

        .tabs-listing .product-tabs li:not(.active):hover a {
            color: #c358a5;
        }

        .tabs-listing .product-tabs li.active a {
            color: #c358a5;
            background-color: #f7e3f1;
        }

        /* Mobile accordion headers follow the same colour scheme. */
        .tabs-listing .tabs-ac-style {
            color: #3f5b3a;
        }

        .tabs-listing .tabs-ac-style.active {
            color: #c358a5;
            background-color: #f7e3f1;
        }

        /* Variant attribute swatches – pink text + pink border only when selected. */
        .product-swatches-option .size-swatches li.active {
            color: #c358a5;
            border-color: #c358a5;
            box-shadow: none;
            background-color: #f7e3f1;
        }

        /* Separator above the Related Products section. */
        .ghs-related-separator {
            border: 0;
            border-top: 1px solid #d9d9d9;
            max-width: 1320px;
            margin: 40px auto 10px;
            opacity: 1;
        }
    </style>
@endonce
