<!--    HERE ENDS YOUR APPLICATION -->
</div>
<div id="no-script">
    <div id="no-script-box">
    <div id="no-script-img"></div>
    <p id="no-script-maintext">
        ¡Vaya! Algo est&aacute; fallando...
    </p>
    <p id="no-script-subtext">
        La ejecuci&oacute;n de esta aplicaci&oacute;n requiere de tener habilitado Javascript, por favor
        <a
        target="_blank" rel="noopener noreferrer"
        href="https://www.enable-javascript.com/">habil&iacute;talo
        </a>
        . Gracias!
    </p>
    </div>
</div>
<button id="btnTop" type="button" class="text-dark " onclick="window.scrollTo(0,0)"><i class="fas fa-arrow-up fs-2"></i></button>
<!--    JS FRAMEWORK    -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.1/mdb.min.js"></script> -->
<!--    JS WEBSITE  -->
<script type="module">
    //  EVENTO SCROLL
    window.addEventListener('scroll', (e) => {
        const btnDiv = document.querySelector('button#btnTop');
        if(window.scrollY >= 100){
            if(!btnDiv.classList.contains('show'))
                btnDiv.classList.add("show")
        }else if(window.scrollY < 100){
            if(btnDiv.classList.contains('show'))
                btnDiv.classList.remove("show")
        }
    });
</script>
</body>
</html> 