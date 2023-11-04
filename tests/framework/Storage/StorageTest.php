<?php

use Framework\Storage\Storage;

describe('Storage tests', function () {
    beforeEach(function () {
        $this->storage = Storage::init(__DIR__.'/fixtures');
    });

    afterEach(function () {
        $this->storage = null;
    });

    it('should return a file object', function () {
        $file = Storage::get('test.txt');

        expect($file)->toBeInstanceOf(\Framework\Storage\File::class);
        expect($file->path())->toBe('test.txt');
        expect($file->contents())->toBe('hello'.PHP_EOL);
        expect($file->mimeType())->toBe('text/plain');
        expect($file->size())->toBe(\strlen('hello'.PHP_EOL));
        expect($file->lastModified())->toBeInstanceOf(\DateTime::class);
        expect($file->visibility())->toBe('public');
    });

    it('should return a jpg image file', function () {
        $file = Storage::get('img/meme.jpg');

        expect($file)->toBeInstanceOf(\Framework\Storage\File::class);
        expect($file->path())->toBe('img/meme.jpg');
        expect($file->mimeType())->toBe('image/jpeg');
        expect($file->size())->toBe(83013);
        expect($file->lastModified())->toBeInstanceOf(\DateTime::class);
        expect($file->visibility())->toBe('public');
    });

    it('should return a png image file', function () {
        $file = Storage::get('img/favicon.png');

        expect($file)->toBeInstanceOf(\Framework\Storage\File::class);
        expect($file->path())->toBe('img/favicon.png');
        expect($file->mimeType())->toBe('image/png');
        expect($file->size())->toBe(28695);
        expect($file->lastModified())->toBeInstanceOf(\DateTime::class);
        expect($file->visibility())->toBe('public');
    });

    it('should list directory contents', function () {
        $dirs = Storage::directories();
        dump($dirs);
        expect($dirs)->toBeArray();
        expect($dirs[0])->toBe('1');
        expect($dirs[1])->toBe('1/1-1');
        expect($dirs[2])->toBe('1/1-1/1-1-1');
        expect($dirs[3])->toBe('1/1-2');
        expect($dirs[4])->toBe('2');
        expect($dirs[5])->toBe('img');
    });

    it('should say if file exists', function () {
        expect(Storage::exists('test.txt'))->toBeTrue();
        expect(Storage::exists('img/meme.jpg'))->toBeTrue();
        expect(Storage::exists('img/favicon.png'))->toBeTrue();
        expect(Storage::exists('img/unknown.png'))->toBeFalse();
    });

    it('should list files in dir', function () {
        $files = Storage::files();
        expect($files)->toBeArray();
        expect($files[0]->path())->toBe('test.txt');

        $files = Storage::files('img');
        expect($files)->toBeArray();
        expect($files[0]->path())->toBe('img/favicon.png');
        expect($files[1]->path())->toBe('img/meme.jpg');
    });

    it('should create a directory', function () {
        Storage::createDirectory('test_create_dir');

        expect(Storage::exists('test_create_dir'))->toBeTrue();

        Storage::deleteDirectory('test_create_dir');
    });

    it('should delete a directory', function () {
        Storage::createDirectory('test_delete_dir');

        expect(Storage::exists('test_delete_dir'))->toBeTrue();

        Storage::deleteDirectory('test_delete_dir');

        expect(Storage::exists('test_delete_dir'))->toBeFalse();
    });

    it('should create a file', function () {
        Storage::put('test_create_file/test.txt', 'hello');

        expect(Storage::exists('test_create_file/test.txt'))->toBeTrue();

        Storage::deleteDirectory('test_create_file');
    });

    it('should delete a file', function () {
        Storage::put('test_delete_file/test.txt', 'hello');

        expect(Storage::exists('test_delete_file/test.txt'))->toBeTrue();

        Storage::delete('test_delete_file/test.txt');

        expect(Storage::exists('test_delete_file/test.txt'))->toBeFalse();

        Storage::deleteDirectory('test_delete_file');
    });

    it('should give public url', function () {
        $url = Storage::url('test.txt');

        expect($url)->toBe('/storage/test.txt');
    });

    it('should copy a file', function () {
        Storage::copy('test.txt', 'test_copy.txt');

        expect(Storage::exists('test_copy.txt'))->toBeTrue();

        Storage::delete('test_copy.txt');
    });

    it('should move a file', function () {
        Storage::put('test_move.txt', 'hello');

        expect(Storage::exists('test_move.txt'))->toBeTrue();

        Storage::move('test_move.txt', 'test_move2.txt');

        expect(Storage::exists('test_move.txt'))->toBeFalse();
        expect(Storage::exists('test_move2.txt'))->toBeTrue();

        Storage::delete('test_move2.txt');
    });

    it('should throw an exception if file not found', function () {
        expect(function () {
            Storage::get('unknown.txt');
        })->toThrow(new \Exception('File not found'));
    });
});
