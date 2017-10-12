<?php

use PHPUnit\Framework\TestCase;

/**
 * Class BuildTest
 */
class BuildTest extends TestCase
{
    protected $buildStatusFile = PROJECT_ROOT . '/data/status.json';

    protected $rebuildMessage = <<<MESSAGE
        It seems that your data was not correctly built. 
        Try to rebuild it by running: `php build/build.php` from project root\n
MESSAGE;


    /**
     * @test
     */
    public function it_checks_if_build_status_file_is_available()
    {
        $this->assertFileIsReadable($this->buildStatusFile, $this->rebuildMessage);
    }

    /**
     * @test
     *
     * @depends it_checks_if_build_status_file_is_available
     */
    public function it_checks_if_data_was_successfully_built()
    {
        $buildStatus = json_decode(file_get_contents($this->buildStatusFile), true);

        $this->assertArrayHasKey('status', $buildStatus, $this->rebuildMessage);
        $this->assertEquals('success', $buildStatus['status'], $this->rebuildMessage);
    }

    /**
     * @test
     *
     * @depends it_checks_if_data_was_successfully_built
     */
    public function it_checks_that_supplemental_file_list_is_available()
    {
        $buildStatus = json_decode(file_get_contents($this->buildStatusFile), true);

        $this->assertNotEmpty($buildStatus['data']['supplemental']['directory'], $this->rebuildMessage);
        $this->assertNotEmpty($buildStatus['data']['supplemental']['file-list'], $this->rebuildMessage);

        $generalDataDirectory = PROJECT_ROOT . $buildStatus['data']['supplemental']['directory']. DIRECTORY_SEPARATOR;
        $this->assertDirectoryIsReadable($generalDataDirectory, $this->rebuildMessage);

        foreach ($buildStatus['data']['supplemental']['file-list'] as $file) {
            $this->assertFileIsReadable($generalDataDirectory . $file, $this->rebuildMessage);
        }
    }

    /**
     * @test
     *
     * @depends it_checks_that_supplemental_file_list_is_available
     */
    public function it_checks_that_supplemental_files_are_available()
    {
        $buildStatus = json_decode(file_get_contents($this->buildStatusFile), true);

        $generalDataDirectory = PROJECT_ROOT . $buildStatus['data']['supplemental']['directory']. DIRECTORY_SEPARATOR;
        $this->assertDirectoryIsReadable($generalDataDirectory, $this->rebuildMessage);

        foreach ($buildStatus['data']['supplemental']['file-list'] as $file) {
            $this->assertFileIsReadable($generalDataDirectory . $file, $this->rebuildMessage);
        }
    }

    /**
     * @test
     *
     * @depends it_checks_if_data_was_successfully_built
     */
    public function it_checks_that_locale_file_list_is_available()
    {
        $buildStatus = json_decode(file_get_contents($this->buildStatusFile), true);

        $this->assertNotEmpty($buildStatus['data']['locales']['directory'], $this->rebuildMessage);
        $this->assertNotEmpty($buildStatus['data']['locales']['file-list'], $this->rebuildMessage);
    }

    /**
     * @test
     *
     * @depends it_checks_that_locale_file_list_is_available
     */
    public function it_checks_that_built_locale_count_is_equal_to_saved_quantity()
    {
        $buildStatus = json_decode(file_get_contents($this->buildStatusFile), true);

        $this->assertNotEmpty($buildStatus['data']['locales']['count']['build'], $this->rebuildMessage);
        $this->assertEquals($buildStatus['data']['locales']['count']['build'], count($buildStatus['data']['locales']['file-list']), $this->rebuildMessage);
    }

    /**
     * @test
     *
     * @depends it_checks_that_built_locale_count_is_equal_to_saved_quantity
     */
    public function it_checks_that_locale_files_are_available()
    {
        $buildStatus = json_decode(file_get_contents($this->buildStatusFile), true);

        $localeDataDirectory = PROJECT_ROOT . $buildStatus['data']['locales']['directory']. DIRECTORY_SEPARATOR;
        $this->assertDirectoryIsReadable($localeDataDirectory, $this->rebuildMessage);

        foreach ($buildStatus['data']['locales']['file-list'] as $locale => $files) {
            $localeDirectory = $localeDataDirectory . $locale . DIRECTORY_SEPARATOR;

            $this->assertDirectoryIsReadable($localeDirectory, $this->rebuildMessage);

            foreach($files as $file) {
                $this->assertFileIsReadable($localeDirectory . $file, $this->rebuildMessage);
            }
        }
    }
}
