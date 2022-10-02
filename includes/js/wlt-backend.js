jQuery(document).ready(function($) {

  $( '.wlt-delete-post' ).on( 'click', function() {

    let self = $( this );
    let parent = self.parents( 'tr' );
    let postID = parent.find( '.wlt_id a' ).attr( 'data-post-id' );

    let ajaxurl = wlt.ajax_url;
    let data = {
      'action'          : 'wlt_delete_post',
      'wlt_post_id'     : postID
    }

    jQuery.post( ajaxurl, data, function( responce ) {
        location.reload( true );
    } );

  } );

  /**
   * Delete multiple data in wp list table
   */
  $( '.action' ).on( 'click', function() {

      let self = $(this);

      let selected = $( '#bulk-action-selector-top' ).val();

      if( 'wlt_edit' == selected || -1 == selected ) {

        alert( 'Please select delete option to delete these rows' );
      }

      let postIDs = [];
      $.each( $( '.wlt-selected' ), function( index, elem ) {
          
          if( $( elem ).prop( 'checked' ) ) {

            let postID = $( elem ).parents( 'tr' ).find( '.wlt_id a' ).attr( 'data-post-id' );
            
            postIDs.push( postID );
          }
      });

      let ajaxurl = wlt.ajax_url;
      let data = {
        'action'      : 'wlt_multiple_delete_post',
        'post_ids'     : postIDs
      }

      jQuery.post( ajaxurl, data, function( responce ) {
          location.reload( true );
      } );

  } );

  /**
   * Edit wp list table data
   */
  $( '.wlt-edit' ).on( 'click', function() {

      let self = $( this );
      let parent = self.parents( 'tr' );
      let postID = parent.find( '.wlt_id a' ).attr( 'data-post-id' );
      let postContent = parent.find( '.wlt_id a' ).attr( 'data-post-content' );
      let postName = parent.find( '.wlt_id a' ).attr( 'data-post-name' );

      $( '.wlt-edit-post-name' ).val( postName );
      $( '.wlt-edit-post-name' ).attr( 'post_id', postID );
      $( '.wlt-edit-post-content' ).val( postContent );

  } );


  $( '.wlt-update-post' ).on( 'click', function() {

      let postID = $( '.wlt-edit-post-name' ).attr( 'post_id' );
      let postContent = $( '.wlt-edit-post-content' ).val();
      let postName = $( '.wlt-edit-post-name' ).val();

      let ajaxurl = wlt.ajax_url;
      let data = {
        'action'      : 'wlt_edit_update_post',
        'post_id'     : postID,
        'post_content': postContent,
        'post_name'   : postName
      }

      jQuery.post( ajaxurl, data, function( responce ) {
          location.reload( true );
      } );

  } );

});