@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $(document).on("click", ".favorite-btn", function() {
                    var button = $(this);
                    var promptId = button.data('id');

                    $.ajax({
                        url: '{{ route('prompt.favorite') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            prompt_id: promptId
                        },
                        success: function(response) {
                            if (response.success) {
                                if (response.message === 'Added to favorites') {
                                    button.find('i').css('color', 'red');
                                } else if (response.message === 'Removed from favorites') {
                                    button.find('i').css('color',
                                        'white');
                                }
                                notify('success', response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = xhr.responseJSON ? xhr.responseJSON.message :
                                'An error occurred';
                            notify('error', errorMessage);
                        }
                    });
                });
            });

        })(jQuery)
    </script>
@endpush
