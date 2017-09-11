Scrapper = {
    loopCounter: 1,
    neededRequests:0,

    preloader: document.querySelector('.preloader'),

    runScrapperBtn: document.getElementById('runScrapper'),
    queueScrapperBtn: document.getElementById('queueScrapper'),

    modalEditBtns: document.getElementsByClassName('modal-edit-btn'),
    modalDeleteBtns: document.getElementsByClassName('modal-delete-btn'),

    confirmPostEditBtns: document.getElementsByClassName('confirm-post-edit'),
    confirmPostDeleteBtns: document.getElementsByClassName('confirm-post-delete'),

    modalEdit: document.getElementById('modalEdit'),
    modalEditPostIdSpan: this.modalEdit.querySelector('.modal-post-id'),
    modalEditImage: this.modalEdit.querySelector('img'),
    modalEditTextInput: this.modalEdit.querySelector('.modal-edit-text-input'),
    modalEditDesriptionInput: this.modalEdit.querySelector('.modal-edit-desription-input'),
    modalEditDateInput: this.modalEdit.querySelector('.modal-edit-date-input'),

    modalDelete: document.getElementById('modalDelete'),
    modalDeletePostIdSpan: this.modalDelete.querySelector('.modal-post-id'),
    modalDeleteImage: this.modalDelete.querySelector('img'),
    modalDeletePostTitle: document.getElementById('modalDeletePostTitle'),

    queueScrapperRequest: function() {
        var self = this,
            xhr = new XMLHttpRequest();
        this.queueScrapperBtn.classList.add('disabled');
        xhr.open('POST', location.pathname + 'queue');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
        xhr.onload = function() {
            self.queueScrapperBtn.classList.remove('disabled');
            alert('Done go and run "php artisan queue:work"')
        };
        xhr.send();
    },

    runScrapperRequest: function() {
        this.loopCounter = 1;
        this.neededRequests = 0;
        this.runScrapperBtn.classList.add('disabled');
        this.preloader.classList.remove('hide');

        var self = this,
            xhr = new XMLHttpRequest();

        xhr.open('POST', location.pathname + 'emptyForFirst');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
        xhr.onload = function() {
            self.neededRequests = JSON.parse(xhr.responseText);
            self.loopCrawlRequest();
        };
        xhr.send();
    },

    loopCrawlRequest: function() {
        var self = Scrapper;
        if (self.loopCounter > self.neededRequests) {
            self.preloader.classList.add('hide');
            location.reload();
            return;
        }
        var data = 'num=' + self.loopCounter,
            xhr = new XMLHttpRequest();
        xhr.open('POST', location.pathname + 'crawl');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
        xhr.onload = function() {
            var response = JSON.parse(xhr.responseText);
            self.loopCounter++;
            self.loopCrawlRequest();
            self.runScrapperBtn.classList.remove('disabled');
        };
        xhr.send(encodeURI(data));
    },

    initEditModal: function(e) {
        var currElem = getClosest(e.target, '.tr-row'),
            itemId = currElem.querySelector('.item-id').innerHTML,
            itemImgSrc = currElem.querySelector('.item-main-image-src').src,
            itemTitle = currElem.querySelector('.item-title').querySelector('a').innerHTML,
            itemDescription = currElem.querySelector('.item-description').innerHTML,
            // todo don't like this date, it should be rewritten to datePicker some
            itemDate = currElem.querySelector('.item-date').innerHTML;
        this.modalEditPostIdSpan.innerHTML = itemId;
        this.modalEditImage.src = itemImgSrc;
        this.modalEditTextInput.value = itemTitle;
        this.modalEditDesriptionInput.value = itemDescription;
        this.modalEditDateInput.value = itemDate;
    },

    initDeleteModal: function(e) {
        var currElem = getClosest(e.target, '.tr-row'),
            itemId = currElem.querySelector('.item-id').innerHTML,
            itemImgSrc = currElem.querySelector('.item-main-image-src').src,
            itemTitle = currElem.querySelector('.item-title').innerHTML;
        this.modalDeletePostIdSpan.innerHTML = itemId;
        this.modalDeleteImage.src = itemImgSrc;
        this.modalDeletePostTitle.innerHTML = itemTitle;
    },

    confirmPostEditRequest: function(e){
        var currBtn = e.target,
            self = this,
            data = 'itemId=' + this.modalEditPostIdSpan.innerHTML
                + '&itemTitle=' + this.modalEditTextInput.value
                + '&itemDescription=' + this.modalEditDesriptionInput.value
                + '&itemDate=' + this.modalEditDateInput.value,
            xhr = new XMLHttpRequest();

        currBtn.classList.add('disabled');

        xhr.open('POST', location.pathname + 'edited');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
        xhr.onload = function() {
            var response = JSON.parse(xhr.responseText);
            if (xhr.status === 200 && response.error !== true) {
                var currPost = document.getElementById('post-'+self.modalEditPostIdSpan.innerHTML);
                currPost.querySelector('.item-title').querySelector('a').innerHTML = self.modalEditTextInput.value;
                currPost.querySelector('.item-description').innerHTML = self.modalEditDesriptionInput.value;
                currPost.querySelector('.item-date').innerHTML = self.modalEditDateInput.value;
                $('#modalEdit').modal('hide');
            } else if (xhr.status !== 200 || response.error === true) {
                alert('Error')
                // ToDo Some Error Handler Toast like in Materialize.css Toast
            }
            currBtn.classList.remove('disabled');
        };
        xhr.send(encodeURI(data));
    },

    confirmPostDeleteRequest: function(e){
        var currBtn = e.target,
            self = this,
            allPostsInPage = document.querySelectorAll('.tr-row'),
            pageLastItemId = allPostsInPage[allPostsInPage.length-1].querySelector('.item-id').innerHTML,
            data = 'itemId=' + this.modalDeletePostIdSpan.innerHTML
                + '&pageLastItemId=' + pageLastItemId,
            xhr = new XMLHttpRequest();

        currBtn.classList.add('disabled');

        xhr.open('POST', location.pathname+'delete');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
        xhr.onload = function() {
            var response = JSON.parse(xhr.responseText);
            if (xhr.status === 200 && response.error !== true) {
                var currPost = document.getElementById('post-'+self.modalDeletePostIdSpan.innerHTML);
                currPost.remove();
                // todo If there is need should append pageLastItemId->next post
                $('#modalDelete').modal('hide');
            } else if (xhr.status !== 200 || response.error === true) {
                alert('Error')
                // ToDo Some Error Handler Toast like in Materialize.css Toast
            }
            currBtn.classList.remove('disabled');
        };
        xhr.send(encodeURI(data));
    },
};
Scrapper.runScrapperBtn.addEventListener('click', Scrapper.runScrapperRequest.bind(Scrapper));
Scrapper.queueScrapperBtn.addEventListener('click', Scrapper.queueScrapperRequest.bind(Scrapper));

Array.prototype.forEach.call(Scrapper.modalEditBtns, (function (element, index, array) {
    element.addEventListener('click', Scrapper.initEditModal.bind(Scrapper));
}));
Array.prototype.forEach.call(Scrapper.modalDeleteBtns, (function (element, index, array) {
    element.addEventListener('click', Scrapper.initDeleteModal.bind(Scrapper));
}));

Array.prototype.forEach.call(Scrapper.confirmPostEditBtns, (function (element, index, array) {
    element.addEventListener('click', Scrapper.confirmPostEditRequest.bind(Scrapper));
}));
Array.prototype.forEach.call(Scrapper.confirmPostDeleteBtns, (function (element, index, array) {
    element.addEventListener('click', Scrapper.confirmPostDeleteRequest.bind(Scrapper));
}));