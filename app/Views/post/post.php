<?= $this->extend('templates/header'); ?>

<div class="container">
    <h2>Share Your Thoughts!</h2>

    <!-- 글쓰기 폼 -->
    <div class="post-form">
        <form id="postForm" action="/posts/create" method="POST" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            
            <!-- 카테고리 선택 -->
            <div class="form-group">
                <select name="category_id" class="form-control" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['category_id'] ?>"><?= esc($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 글 내용 -->
            <div class="form-group">
                <textarea name="content" rows="3" class="form-control" placeholder="What's on your mind?" required></textarea>
            </div>

            <!-- 여러 이미지 업로드 -->
            <div class="form-group">
                <input type="file" name="image[]" id="imageUpload" accept="image/*" multiple>
                <div id="imagePreviewContainer"></div>
            </div>

            <button type="submit" class="btn btn-primary" id="submitPost">Post</button>
        </form>
    </div>

    <!-- 게시물 피드 -->
    <div class="post-feed">
        <?php foreach ($posts as $post): ?>
            <div class="post-item">
                <p><?= esc($post['content']) ?></p>
                
                <!-- 여러 이미지 출력 -->
                <?php if ($post['image']): ?>
                    <?php foreach (json_decode($post['image']) as $imagePath): ?>
                        <img src="<?= base_url($imagePath) ?>" style="width: 100%; max-height: 300px; margin-top: 10px;">
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <div class="post-actions">
                    <button class="like-btn" data-post-id="<?= $post['post_id'] ?>">Like</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // 좋아요 버튼 클릭 이벤트
        $('.like-btn').click(function(event) {
            event.preventDefault();
            var post_id = $(this).data('post-id');
            var button = $(this);
            
            $.ajax({
                url: '/posts/like/' + post_id,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        if (response.liked) {
                            button.text('Unlike');
                        } else {
                            button.text('Like');
                        }
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert("Error occurred while liking the post.");
                }
            });
        });

        // 이미지 업로드 미리보기 기능
        $('#imageUpload').on('change', function() {
            $('#imagePreviewContainer').empty(); // 기존 미리보기 이미지 삭제
            var files = this.files;

            if (files) {
                $.each(files, function(index, file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreviewContainer').append(
                            '<img src="' + e.target.result + '" style="width: 100px; height: 100px; margin: 5px; border-radius: 5px;">'
                        );
                    }
                    reader.readAsDataURL(file);
                });
            }
        });

        // submitPost 버튼 클릭 이벤트
        $('#submitPost').click(function(event) {
            event.preventDefault();

            var formData = new FormData($('#postForm')[0]); // form 데이터를 가져옴
            $.ajax({
                url: '/posts/create', // 글 작성 URL
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        alert("Post created successfully.");
                        location.reload();
                    } else {
                        alert(response.error || "An error occurred while posting. Please try again.");
                    }
                },
                error: function(xhr, status, error) {
                    alert("Error: " + xhr.status + " - " + error);
                }
            });
        });
    });
</script>

<style>
    .container { max-width: 600px; margin: auto; padding: 20px; }
    .post-form { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 5px; background-color: #f9f9f9; }
    .post-feed { border-top: 1px solid #ccc; padding-top: 15px; }
    .post-item { padding: 10px; margin-bottom: 15px; border-bottom: 1px solid #eee; }
    .post-actions { margin-top: 10px; }
    .like-btn { background: none; border: none; color: blue; cursor: pointer; font-size: 14px; }
    #imagePreviewContainer img { display: inline-block; margin: 5px; }
</style>

<?= $this->include('templates/footer'); ?>
