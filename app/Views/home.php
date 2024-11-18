<?= $this->include('templates/header'); ?>

<div class="container">
    <h2>Welcome to the Vegan Community!</h2>
    <p>Share your thoughts and connect with others!</p>

    <!-- 카테고리 버튼 -->
    <div class="category-buttons">
        <button class="btn btn-secondary" onclick="loadPosts(0)">All</button>
        <button class="btn btn-secondary" onclick="loadPosts(1)">비건뉴스</button>
        <button class="btn btn-secondary" onclick="loadPosts(2)">비건 레시피</button>
        <button class="btn btn-secondary" onclick="loadPosts(3)">자유게시판</button>
        <button class="btn btn-secondary" onclick="loadPosts(4)">유머글</button>
        <button class="btn btn-secondary" onclick="loadPosts(5)">비건 식당 리뷰</button>
        <button class="btn btn-secondary" onclick="loadPosts(6)">비건 제품 추천</button>
        <button class="btn btn-secondary" onclick="loadPosts(7)">환경 이야기</button>
        <button class="btn btn-secondary" onclick="loadPosts(8)">비건 여행</button>
    </div>

    <!-- 글쓰기 섹션 -->
    <div class="post-form">
        <form id="postForm" enctype="multipart/form-data">
            <?= csrf_field(); ?>

            <!-- 카테고리 선택 -->
            <div class="form-group">
                <label for="postCategory">Category:</label>
                <select id="postCategory" name="category_id" class="form-control">
                    <option value="1">비건뉴스</option>
                    <option value="2">비건 레시피</option>
                    <option value="3">자유게시판</option>
                    <option value="4">유머글</option>
                    <option value="5">비건 식당 리뷰</option>
                    <option value="6">비건 제품 추천</option>
                    <option value="7">환경 이야기</option>
                    <option value="8">비건 여행</option>
                </select>
            </div>

            <!-- 게시글 입력 -->
            <div class="form-group">
                <textarea id="postContent" name="content" rows="3" class="form-control" placeholder="What's on your mind?"></textarea>
            </div>

            <!-- 이미지 업로드 -->
            <div class="form-group">
                <input type="file" id="imageInput" name="images[]" accept="image/*" class="form-control" multiple>
                <div id="imagePreviewContainer" class="image-preview-container"></div>
            </div>

            <!-- 게시 버튼 -->
            <button type="button" id="submitPost" class="btn btn-primary">Post</button>
        </form>
    </div>

    <!-- 게시물 피드 -->
    <div id="postFeed" class="post-feed">
        <h3>Recent Posts</h3>
        <!-- 게시물이 여기에 로드됩니다 -->
    </div>
</div>

<script>
    let imageFiles = [];
    let selectedCategory = 0;

    document.addEventListener('DOMContentLoaded', function () {
        // 초기 게시글 로드
        loadPosts(selectedCategory);

        // 게시 버튼 클릭 이벤트
        document.getElementById('submitPost').addEventListener('click', function () {
            const button = this;
            button.disabled = true; // 중복 클릭 방지

            const formData = new FormData(document.getElementById('postForm'));
            imageFiles.forEach(file => formData.append('images[]', file));
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            fetch('/post/create', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    button.disabled = false; // 요청 완료 후 버튼 활성화
                    if (data.success) {
                        document.getElementById('postContent').value = '';
                        document.getElementById('imagePreviewContainer').innerHTML = ''; // 미리보기 초기화
                        document.getElementById('imageInput').value = ''; // 파일 입력 초기화
                        imageFiles = []; // 파일 배열 초기화
                        loadPosts(selectedCategory);
                    } else {
                        alert(data.message || 'Error occurred.');
                    }
                })
                .catch(error => {
                    button.disabled = false;
                    console.error('Error:', error);
                });
        });

        // 이미지 선택 이벤트
// 이미지 선택 이벤트
        document.getElementById('imageInput').addEventListener('change', function (event) {
            const files = event.target.files;
            const container = document.getElementById('imagePreviewContainer');
            container.innerHTML = '';
            imageFiles = []; // 배열 초기화

            Array.from(files).forEach((file, index) => {
                if (index < 10) {
                    imageFiles.push(file);
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.width = '100px';
                    img.style.margin = '5px';
                    img.onclick = () => removeImage(index);
                    container.appendChild(img);
                } else {
                    alert('You can upload up to 10 images.');
                }
            });
        });

    function loadPosts(category) {
        selectedCategory = category;
        fetch(`/post/list?category_id=${category}`)
            .then(response => response.json())
            .then(posts => {
                const postFeed = document.getElementById('postFeed');
                postFeed.innerHTML = '';
                if (posts.length > 0) {
                    posts.forEach(post => {
                        const postItem = `<div class="post-item">
                            <p><strong>${post.category_name}</strong></p>
                            <p>${post.content}</p>
                            ${
                                post.images.length > 0
                                    ? post.images.map(img => `<img src="${img.file_path}" style="width:100%;max-height:200px;">`).join('')
                                    : ''
                            }
                        </div>`;
                        postFeed.innerHTML += postItem;
                    });
                } else {
                    postFeed.innerHTML = '<p>No posts available.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching posts:', error);
            });
    }

    function removeImage(index) {
        imageFiles.splice(index, 1);
        document.getElementById('imageInput').dispatchEvent(new Event('change'));
    }
</script>

<style>
    .post-form {
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    .post-feed {
        margin-top: 20px;
    }
    .post-item {
        padding: 10px;
        border-bottom: 1px solid #ccc;
        margin-bottom: 10px;
    }
    .image-preview-container img {
        cursor: pointer;
    }
</style>

<?= $this->include('templates/footer'); ?>
