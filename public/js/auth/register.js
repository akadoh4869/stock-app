document.getElementById('user_name').addEventListener('input', function() {
            var userName = this.value;
            var errorElement = document.getElementById('username-error');

            if (userName.length > 0) {
                fetch('{{ route("check.username") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ user_name: userName })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        errorElement.style.display = 'block';
                    } else {
                        errorElement.style.display = 'none';
                    }
                });
            } else {
                errorElement.style.display = 'none';
            }
        });