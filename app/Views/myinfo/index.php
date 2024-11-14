<?= $this->extend('templates/header'); ?>

<?= $this->section('content'); ?>

<div class="container">
    <h2>내 정보</h2>

    <form id="updateInfoForm" method="POST">
        <div class="form-group">
            <label for="username">아이디</label>
            <input type="text" id="username" class="form-control" value="<?= esc($user['username']); ?>" readonly>
        </div>

        <div class="form-group">
            <label for="email">이메일</label>
            <input type="email" id="email" class="form-control" value="<?= esc($user['email']); ?>" readonly>
        </div>

        <div class="form-group">
            <label for="nickname">닉네임</label>
            <input type="text" id="nickname" name="nickname" class="form-control" value="<?= esc($user['nickname']); ?>">
        </div>

        <div class="form-group">
            <label for="phone">전화번호</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?= esc($user['phone']); ?>" placeholder="010-xxxx-xxxx">
            <small id="phoneError" class="text-danger" style="display:none;">전화번호는 010-xxxx-xxxx 형식으로 입력하세요.</small>
        </div>

        <div class="form-group">
            <label for="profileImage">프로필 이미지</label>
            <div>
                <img id="profileImagePreview" src="<?= esc($user['profile_image'] ?? '/img/basic.png'); ?>" alt="프로필 이미지" style="width: 100px; height: 100px; display: block; margin-bottom: 10px;">
            </div>
            <input type="file" id="profileImage" name="profile_image" accept="image/*">
        </div>

        <div class="form-group">
            <label for="name">이름</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= esc($user['name']); ?>">
            <small id="nameError" class="text-danger" style="display:none;">이름은 두 글자 이상 입력해야 합니다.</small>
        </div>

        <button type="button" id="updateButton" class="btn btn-primary">정보 수정</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // 전화번호 필드에서 숫자만 입력받고 010-xxxx-xxxx 형식으로 자동 포맷
        $('#phone').on('input', function() {
            var input = $(this).val().replace(/[^0-9]/g, ''); // 숫자만 남김
            if (input.length > 3 && input.length <= 7) {
                input = input.replace(/(\d{3})(\d+)/, '$1-$2');
            } else if (input.length > 7) {
                input = input.replace(/(\d{3})(\d{4})(\d+)/, '$1-$2-$3');
            }
            $(this).val(input);
        });

        // 정보 수정 버튼 클릭 시 입력 값 유효성 검사
        $('#updateButton').click(function(event) {
            event.preventDefault();
            var valid = true;
            
            // 닉네임 유효성 검사
            if ($('#nickname').val().trim() === '') {
                valid = false;
                alert("닉네임을 입력해주세요.");
            }
            
            // 이름 유효성 검사
            if ($('#name').val().trim().length < 2) {
                $('#nameError').show();
                valid = false;
            } else {
                $('#nameError').hide();
            }

            // 전화번호 유효성 검사
            var phonePattern = /^010-\d{4}-\d{4}$/;
            if (!phonePattern.test($('#phone').val())) {
                $('#phoneError').show();
                valid = false;
            } else {
                $('#phoneError').hide();
            }

            if (!valid) return;

            // 폼 데이터 전송
            var formData = {
                nickname: $('#nickname').val(),
                phone: $('#phone').val(),
                name: $('#name').val(),
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            };

            $.ajax({
                url: '/myinfo/update',
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert("정보 수정 중 오류가 발생했습니다. 다시 시도해 주세요.");
                }
            });
        });

        // 프로필 이미지 미리보기 및 업로드
        $('#profileImage').change(function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#profileImagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

            var formData = new FormData();
            formData.append('profile_image', this.files[0]);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            $.ajax({
                url: '/myinfo/uploadProfileImage',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#profileImagePreview').attr('src', response.filePath);
                    } else {
                        alert(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert("이미지 업로드 중 오류가 발생했습니다. 다시 시도해 주세요.");
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>
