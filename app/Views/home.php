<?= $this->include('templates/header'); ?>

<div class="container">
    <h2>Welcome to the Vegan Community!</h2>
    <p>This is the homepage. Join us to connect with others and share your journey.</p>

    <!-- 게시물 작성 영역 -->
    <div class="post-form">
        <h3>What's on your mind?</h3>
        <form id="postForm" method="POST" enctype="multipart/form-data">
            <?= csrf_field(); ?>

            <!-- 텍스트 입력 -->
            <div class="form-group">
                <textarea id="postContent" name="content" rows="3" class="form-control" placeholder="Share your thoughts..."></textarea>
            </div>

            <!-- 이미지 업로드 -->
            <div class="form-group">
                <label for="postImage">Upload an image (optional):</label>
                <input type="file" id="postImage" name="image" accept="image/*" class="form-control">
                <img id="imagePreview" style="display: none; width: 100%; max-height: 300px; margin-top: 10px;" alt="Image Preview">
            </div>

            <!-- 게시 버튼 -->
            <button type="button" id="submitPost" class="btn btn-primary">Post</button>
        </form>
    </div>

    <!-- 게시물 피드 -->
    <div id="postFeed" class="post-feed">
        <h3>Recent Posts</h3>
        <!-- 게시물이 이곳에 추가됩니다 -->
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // 이미지 선택 시 미리보기 표시
        $('#postImage').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        // 게시 버튼 클릭 시 AJAX 요청으로 게시물 전송
        $('#submitPost').click(function() {
            const formData = new FormData();
            formData.append('content', $('#postContent').val());
            formData.append('image', $('#postImage')[0].files[0]);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            $.ajax({
                url: '/post/create',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // 게시물이 성공적으로 추가되면 피드에 새 게시물 표시
                        const newPost = `<div class="post-item">
                                            <p>${response.content}</p>
                                            ${response.image ? `<img src="${response.image}" alt="Post Image" style="width:100%; max-height: 300px;">` : ''}
                                         </div>`;
                        $('#postFeed').prepend(newPost);
                        $('#postContent').val(''); // 텍스트 필드 초기화
                        $('#imagePreview').hide();  // 이미지 미리보기 초기화
                        $('#postImage').val('');    // 이미지 입력 초기화
                    } else {
                        alert('Failed to post. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred while posting. Please try again.');
                }
            });
        });
    });
</script>

<style>
    .container {
        max-width: 600px;
        margin: auto;
    }
    .post-form {
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .post-feed {
        border-top: 1px solid #ccc;
        padding-top: 15px;
    }
    .post-item {
        padding: 10px;
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
</style>

<?= $this->include('templates/footer'); ?>
