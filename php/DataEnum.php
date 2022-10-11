<?php

const MEMBER_TYPE_MEMBER = "member";
const MEMBER_TYPE_ADMIN = "admin";

const UPLOAD_IMAGE_TYPES = array("jpg", "jpeg", "png", "bmp", "webp", "svg", "gif");
const BOOK_LANGUAGES = array("English","French","German","Mandarin","Japanese","Russian","Other");
const BOOK_CATEGORIES = array("Fiction","Nonfiction","Reference");
const BOOK_SEARCH_COLUMN_NAME = array("book_title", "author", "publisher", "language", "category", "any");
const ADMIN_SEARCH_TYPE = array("all","exa","fuz","one", "borrow");
const ADMIN_UPDATE_TYPE = array("add", "edit", "delete", "return");
const USER_LOGIN_SESSION_NAME = "libraryLoginUser";