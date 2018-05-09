<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const cb_autobaud         = $( "#autobaud" );
    const div_autobaud        = $( "#autobaud-section" );

    $(document).ready(function(){
        cb_autobaud.change( );

    });

    /**
     * display or hide the autobaud area
     */
    cb_autobaud.change( function(){
        if( this.checked ){
            div_autobaud.hide();
        } else {
            div_autobaud.show();
        }
    });
</script>