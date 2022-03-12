// require(['jquery', 'jquery/ui'], function ($) {
//     function search(searchString = null) {
//
//         var baseUrl = window.baseUrl;
//         var url = baseUrl + '/search/search/index'; // 'www' to be removed when the server is changed
//         var url = url.replace("#", "");
//
//         jQuery('#search-results').empty();
//         jQuery('.loader-gif').css('display','block');
//
//         // if (!searchString) {
//         //     var searchString = jQuery('#search-location').val();
//         // }
//
//         jQuery.ajax({
//             url: url,
//             method: 'post',
//             data: {'searchString': searchString},
//         }).done(function (answer) {
//             jQuery('.loader-gif').css('display','none');
//             jQuery('#search-results').html(answer);
//             jQuery('#search-results').show();
//         })
//     }
//
//     jQuery('#find-search-location').on('click', function () {
//             search(jQuery('#find-search-location').val());
//     });
//
// });
