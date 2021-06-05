<?php
add_action('wp_footer', 'add_script');
function add_script() { ?>

<script>
/* Изменения путя для тега img */
let imgLength = document.querySelectorAll("img")
for (let i = 0; i < imgLength.length; i++) {
    let imgSrcAtrr = imgLength[i].getAttribute("src")

    if (imgSrcAtrr.substring(0, 4) !== 'http' && imgSrcAtrr.substring(0, 4) !== '//te') {
        imgLength[i].setAttribute('src', <?php echo get_template_directory_uri() ?>/images/${imgSrcAtrr})
    }

    if (imgLength[i].getAttribute("alt") === "") {
        imgLength[i].setAttribute('alt', 'Лучше чем ничего')
    }
}
/*Изминение путя для picture*/
let pictureLength = document.querySelectorAll("picture source")
for (let i = 0; i < pictureLength.length; i++) {
    let sourceSrcAtrr = pictureLength[i].getAttribute("srcset")
    pictureLength[i].setAttribute('srcset',
        <?php echo get_template_directory_uri() ?>/images/${sourceSrcAtrr})
}
</script>
<?php }