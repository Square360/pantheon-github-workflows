<?php

namespace Square360\PantheonWorkflows;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\ScriptEvents;
use Composer\Script\Event;

/**
 * Composer plugin for Square360 Pantheon GitHub Workflows package.
 */
class ComposerPlugin implements PluginInterface, EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public function activate(Composer $composer, IOInterface $io) {
    // Plugin activation - no action needed
  }

  /**
   * {@inheritdoc}
   */
  public function deactivate(Composer $composer, IOInterface $io) {
    // Plugin deactivation - no action needed
  }

  /**
   * {@inheritdoc}
   */
  public function uninstall(Composer $composer, IOInterface $io) {
    // Plugin uninstall - no action needed
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      ScriptEvents::POST_INSTALL_CMD => 'onPostInstall',
      ScriptEvents::POST_UPDATE_CMD => 'onPostUpdate',
    ];
  }

  /**
   * Handle post-install event.
   *
   * @param \Composer\Script\Event $event
   *   The Composer event object.
   */
  public function onPostInstall(Event $event) {
    ComposerScripts::postInstall($event);
  }

  /**
   * Handle post-update event.
   *
   * @param \Composer\Script\Event $event
   *   The Composer event object.
   */
  public function onPostUpdate(Event $event) {
    ComposerScripts::postUpdate($event);
  }

}