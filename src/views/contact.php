<?php
// Defaults to avoid undefined index notices
$errors = $errors ?? [];
$old = $old ?? ['name' => '', 'email' => '', 'message' => ''];
$success = $success ?? null;
$title = $title ?? 'Contact';
?>

<h1>Contact</h1>

<h2>On social media</h2>
<p>
    Find me on
    <strong><a href="https://www.threads.com/@ferencfaluvegi" target="_blank" rel="nofollow">Threads</a></strong> and
    <strong><a href="https://www.instagram.com/ferencfaluvegi/" target="_blank" rel="nofollow">Instagram</a></strong>
    by the handle <strong>@ferencfaluvegi</strong>.
</p>
<p>
    I'm still hiding on <strong><a href="https://faluvegiferenc.substack.com" target="_blank" rel="nofollow">Substack</a></strong>,
    but this will change soon.
</p>


<h2 class="mt-10">Via e-mail</h2>
<p>
    You can contact me by e-mail. I check my inbox regularly.
</p>
<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $field => $msg): ?>
                <li><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form action="/contact" method="post" class="form-container">
    <div class="form-group">
        <label for="name">Name</label>
        <input
            type="text"
            id="name"
            name="name"
            value="<?= $old['name'] ?? '' ?>"
            required
            maxlength="100"
            pattern="^[A-Za-z0-9 .,'-]{2,100}$"
            title="Please enter 2-100 characters. Letters (A-Z), numbers, spaces, and . , ' - allowed."
        />
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= $old['email'] ?? '' ?>"
            required
        />
    </div>

    <div class="form-group">
        <label for="message">Message</label>
        <textarea
            id="message"
            name="message"
            rows="6"
            required
            maxlength="5000"
        ><?= $old['message'] ?? '' ?></textarea>
    </div>

    <div class="form-group">
        <label>Captcha</label>
        <div class="captcha-wrapper">
            <img src="<?= $captcha ?>" alt="Captcha" />
        </div>
        <input
            type="text"
            name="captcha"
            placeholder="Enter the text from the image"
            required
            maxlength="10"
        />
    </div>

    <div class="form-actions">
        <button type="submit">Send message</button>
    </div>
</form>