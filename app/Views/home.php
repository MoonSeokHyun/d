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

            <div class="form-group">
                <textarea id="postContent" name="content" rows="3" class="form-control" placeholder="What's on your mind?"></textarea>
            </div>

            <div class="form-group">
                <input type="file" id="imageInput" name="images[]" accept="image/*" class="form-control" multiple>
                <div id="imagePreviewContainer" class="image-preview-container"></div>
            </div>

            <button type="button" id="submitPost" class="btn btn-primary">Post</button>
        </form>
    </div>

    <!-- 게시물 피드 -->
    <div id="postFeed" class="post-feed">
        <h3>Recent Posts</h3>
    </div>

    <!-- 디테일 모달 -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="detailContent"></div>
        </div>
    </div>
</div>

<script>
    let selectedCategory = 0;
    let currentPage = 1;

    document.addEventListener('DOMContentLoaded', function () {
        loadPosts(selectedCategory);

        document.getElementById('submitPost').addEventListener('click', createPost);

        document.getElementById('imageInput').addEventListener('change', function (event) {
            const files = event.target.files;
            const container = document.getElementById('imagePreviewContainer');
            container.innerHTML = '';

            Array.from(files).forEach(file => {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.width = '100px';
                img.style.margin = '5px';
                container.appendChild(img);
            });
        });

        window.addEventListener('scroll', function () {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
                currentPage++;
                loadPosts(selectedCategory, currentPage);
            }
        });
    });

    function loadPosts(category, page = 1) {
        selectedCategory = category;

        if (category !== 0) {
            document.getElementById('postCategory').value = category;
        }

        fetch(`/post/list?category_id=${category}&page=${page}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                return response.json();
            })
            .then(posts => {
                const postFeed = document.getElementById('postFeed');

                if (page === 1) {
                    postFeed.innerHTML = '';
                }

                posts.forEach(post => {
                    const postItem = document.createElement('div');
                    postItem.classList.add('post-item');
                    postItem.innerHTML = `
                        <p><strong>${post.category_name}</strong></p>
                        <p>${post.content}</p>
                        ${
                            post.images.length
                                ? `<div class="image-slider">
                                    <div class="slider-container">
                                        ${post.images.map(img => `
                                            <div class="slider-item">
                                                <img src="${img.file_path}" style="width:100%; max-height:400px;">
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>`
                                : ''
                        }
                        <button class="btn btn-info" onclick="showDetail(${post.post_id})">View Details</button>
                    `;
                    postFeed.appendChild(postItem);
                });
            })
            .catch(error => console.error(`Error fetching posts: ${error.message}`));
    }

    function createPost() {
        const formData = new FormData(document.getElementById('postForm'));
        fetch('/post/create', {
            method: 'POST',
            body: formData,
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  document.getElementById('postForm').reset();
                  loadPosts(selectedCategory, 1);
              } else {
                  alert(data.message || 'Failed to create post.');
              }
          });
    }

    function showDetail(postId) {
        fetch(`/post/detail/${postId}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                return response.json();
            })
            .then(post => {
                const modal = document.getElementById('detailModal');
                const content = document.getElementById('detailContent');
                content.innerHTML = `
                    <h3>${post.category_name}</h3>
                    <p>${post.content}</p>
                    ${
                        post.images.length
                            ? `<div class="image-slider">
                                <div class="slider-container">
                                    ${post.images.map(img => `
                                        <div class="slider-item">
                                            <img src="${img.file_path}" style="width:100%; max-height:400px;">
                                        </div>
                                    `).join('')}
                                </div>
                            </div>`
                            : ''
                    }
                `;
                modal.style.display = 'block';
            })
            .catch(error => console.error(`Error fetching post detail: ${error.message}`));
    }

    function closeModal() {
        document.getElementById('detailModal').style.display = 'none';
    }
</script>

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 10;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
    }
    .modal-content {
        background-color: white;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 10px;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover, .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<?= $this->include('templates/footer'); ?>
