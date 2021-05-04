<footer id="colophon" class="site-footer">
    <div class="footer-wrapper">
        подвал
        <script>
        let pictureLength = document.querySelectorAll("picture")
        for (let i = 0; i < pictureLength.length; i++) {
            let imgSrc = pictureLength[i].querySelector("img")
            let sourceSrc = pictureLength[i].querySelector("source")

            let imgSrcAtrr = imgSrc.getAttribute("src")
            let sourceSrcAtrr = sourceSrc.getAttribute("srcset")

            imgSrc.setAttribute('src', `<?php echo get_template_directory_uri() ?>/${imgSrcAtrr}`)
            sourceSrc.setAttribute('srcset', `<?php echo get_template_directory_uri() ?>/${sourceSrcAtrr}`)

            let imgAlt = imgSrc.getAttribute("alt")
            if (imgAlt === "") {
                imgSrc.setAttribute('alt', 'Лучше чем ничего')
            }
        }
        </script>
    </div>
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>