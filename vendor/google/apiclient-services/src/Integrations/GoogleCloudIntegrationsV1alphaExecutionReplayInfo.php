<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaExecutionReplayInfo extends \Google\Collection
{
  protected $collection_key = 'replayedExecutionInfoIds';
  /**
   * @var string
   */
  public $originalExecutionInfoId;
  /**
   * @var string
   */
  public $replayMode;
  /**
   * @var string
   */
  public $replayReason;
  /**
   * @var string[]
   */
  public $replayedExecutionInfoIds;

  /**
   * @param string
   */
  public function setOriginalExecutionInfoId($originalExecutionInfoId)
  {
    $this->originalExecutionInfoId = $originalExecutionInfoId;
  }
  /**
   * @return string
   */
  public function getOriginalExecutionInfoId()
  {
    return $this->originalExecutionInfoId;
  }
  /**
   * @param string
   */
  public function setReplayMode($replayMode)
  {
    $this->replayMode = $replayMode;
  }
  /**
   * @return string
   */
  public function getReplayMode()
  {
    return $this->replayMode;
  }
  /**
   * @param string
   */
  public function setReplayReason($replayReason)
  {
    $this->replayReason = $replayReason;
  }
  /**
   * @return string
   */
  public function getReplayReason()
  {
    return $this->replayReason;
  }
  /**
   * @param string[]
   */
  public function setReplayedExecutionInfoIds($replayedExecutionInfoIds)
  {
    $this->replayedExecutionInfoIds = $replayedExecutionInfoIds;
  }
  /**
   * @return string[]
   */
  public function getReplayedExecutionInfoIds()
  {
    return $this->replayedExecutionInfoIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaExecutionReplayInfo::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaExecutionReplayInfo');
