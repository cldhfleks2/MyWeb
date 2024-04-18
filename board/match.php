<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/head.php');

function getTierName($tier) {
    $tierMap = [
        '1' => 'Iron 4', '2' => 'Iron 3', '3' => 'Iron 2', '4' => 'Iron 1',
        '5' => 'Bronze 4', '6' => 'Bronze 3', '7' => 'Bronze 2', '8' => 'Bronze 1',
        '9' => 'Silver 4', '10' => 'Silver 3', '11' => 'Silver 2', '12' => 'Silver 1',
        '13' => 'Gold 4', '14' => 'Gold 3', '15' => 'Gold 2', '16' => 'Gold 1',
        '17' => 'Platinum 4', '18' => 'Platinum 3', '19' => 'Platinum 2', '20' => 'Platinum 1',
        '21' => 'Emerald 4', '22' => 'Emerald 3', '23' => 'Emerald 2', '24' => 'Emerald 1',
        '25' => 'Diamond 4', '26' => 'Diamond 3', '27' => 'Diamond 2', '28' => 'Diamond 1',
        '30' => 'Master ~',
    ];
    return $tierMap[$tier] ?? 'Unknown';
}

//////////////////////////////////////////////////////////////////////////////////////////////

//1. {AB}{CD} // {AC}{BD} // {AD}{BC} : 가능한 모든 팀조합을 생성
function generateGroups($players) {
    $totalPlayers = count($players);
    $validGroups = [];

    // 두 그룹 중 한 그룹이 요소가 하나 더 많게 나누기 위해 사용되는 변수
    $halfPlayers = ceil($totalPlayers / 2);

    // 플레이어 목록을 조합하여 가능한 모든 그룹을 생성
    for ($i = 1; $i < (1 << $totalPlayers); $i++) {
        // 현재 비트 마스크를 기반으로 선택된 플레이어를 저장할 배열
        $currentGroup = [];
        
        // 비트마스크에서 1인 비트의 인덱스를 찾아 해당 플레이어를 선택
        for ($j = 0; $j < $totalPlayers; $j++) {
            if ($i & (1 << $j)) {
                $currentGroup[] = $players[$j]['id'];
            }
        }
        
        // 두 그룹 중 한 그룹이 요소가 하나 더 많은 경우를 고려하여 조합
        if (count($currentGroup) == $halfPlayers) {
            $otherGroup = array_diff(array_column($players, 'id'), $currentGroup);
            sort($currentGroup);
            sort($otherGroup);
            // 중복을 제거하고 유효한 경우만 추가
            $isDuplicate = false;
            foreach ($validGroups as $group) {
                if ($group[0] == $currentGroup || $group[1] == $currentGroup) {
                    $isDuplicate = true;
                    break;
                }
            }
            if (!$isDuplicate) {
                $validGroups[] = [$currentGroup, $otherGroup];
            }
        }
    }
    
    return $validGroups;
}

//2. {12}{34} // {13}{24} // {14}{23} : 팀조합경우를 티어밸류로변환
function modifyGroups($players, $groups) {
    // 결과를 저장할 배열
    $modifiedGroups = [];
    
    // 각 그룹에 대해 반복
    foreach ($groups as $group) {
        $modifiedGroup = [];
        // 그룹 내의 각 플레이어에 대해 반복
        foreach ($group as $team) {
            $modifiedTeam = [];
            // 그룹 내의 각 플레이어의 id를 tier로 변환
            foreach ($team as $playerId) {
                foreach ($players as $player) {
                    if ($player['id'] === $playerId) {
                        $modifiedTeam[] = $player['tier'];
                        break;
                    }
                }
            }
            // 변환된 팀을 그룹에 추가
            $modifiedGroup[] = $modifiedTeam;
        }
        // 변환된 그룹을 결과 배열에 추가
        $modifiedGroups[] = $modifiedGroup;
    }
    
    return $modifiedGroups;
}

//$players는 안쓰이긴함
//3. {3}{7} // {4}{6} // {5}{5} : 같은 팀 티어밸류 총합으로 변환
function calculateGroups($players, $modifiedGroups) {
    $calGroups = [];

    foreach ($modifiedGroups as $group) {
        $calGroup = [];
        foreach ($group as $team) {
            // 그룹 내의 티어 값을 더하여 새로운 그룹을 생성
            $calTeam = array_sum($team);
            $calGroup[] = $calTeam;
        }
        // 새로운 그룹을 결과 배열에 추가
        $calGroups[] = $calGroup;
    }

    return $calGroups;
}

//4. 결과적으로 4차원배열생성
//$resultGroups[][0] => 모든 경우에서 두 그룹의 티어밸런스차이를 구함 (적을수록 균등한것.)
//$resultGroups[][1][0~1] => 두 그룹
function makeGroups($players, $calGroups, $generateGroups) {
    $resultGroups = [];

    foreach ($calGroups as $index => $calGroup) {
        $difference = abs($calGroup[0] - $calGroup[1]);
        $resultGroups[$index][0] = $difference;
        $resultGroups[$index][1] = $generateGroups[$index];
    }

    // $resultGroups를 $resultGroups[][0] 기준으로 정렬
    usort($resultGroups, function($a, $b) {
        return $a[0] <=> $b[0];
    });

    return $resultGroups;
}

//5. 입력받은 $ratio를 퍼센트로 변환해서 해당 팀차이밸런스에대한 팀조합을 $red_team과 $blue_team으로 리턴
function convertRatioToPercent($players, $ratio, $resultGroups) {
    // $resultGroups의 1차원 인덱스 총 갯수
    $totalIndices = count($resultGroups);

    // 퍼센트(%) 값을 계산하여 정수로 변환
    $percent = $ratio * 100;
    
    // ceil(N * P) 값을 계산
    $resultIndex = ceil($totalIndices * $percent / 100) - 1; //인덱스로 변환하기위해 -1

    // 결과 반환
    $red_team = [];
    $blue_team = [];

    // 각 팀의 구성 만들기
    foreach ($resultGroups[$resultIndex][1][0] as $id) {
        foreach ($players as $player) {
            if ($player['id'] === $id) {
                $red_team[] = ['id' => $player['id'], 'tier' => $player['tier']];
                break;
            }
        }
    }
    
    foreach ($resultGroups[$resultIndex][1][1] as $id) {
        foreach ($players as $player) {
            if ($player['id'] === $id) {
                $blue_team[] = ['id' => $player['id'], 'tier' => $player['tier']];
                break;
            }
        }
    }

    // 팀 순서 랜덤하게 결정하여 반환
    if (mt_rand(0, 1) === 0) {
        return [$red_team, $blue_team];
    } else {
        return [$blue_team, $red_team];
    }
}
///////////////////////////////////////////////////////////////////////////////////////////


// 초기값 설정
$players = array();
$player_count = isset($_POST['player_count']) ? $_POST['player_count'] : 2; // 이전 인원수를 유지
$ratio = 0.01; 

// 내전표만들기를 눌렀을때 작동
if(isset($_POST['submit'])) { //데이터 전송 신호가 왔을때
    // 이전에 입력된 플레이어 정보를 가져옴
    for ($i = 1; $i <= $player_count; $i++) {
        $id_key = "player_${i}_id";
        $tier_key = "player_${i}_tier";
        $id = $_POST[$id_key] ?? '';
        $tier = $_POST[$tier_key] ?? '';
        if (empty($id) || empty($tier)) {
            // 입력칸이 비어있을 경우 에러 메시지 출력 후 종료
            exit('<script>alert("입력칸을 채워주세요.");</script>');
        }
        $players[] = array('id' => $id, 'tier' => $tier);
    }
    
    //값이 없으면 0으로 지정. 두팀이 균등하게
    $ratio = isset($_POST['teamBalance']) ? $_POST['teamBalance'] : 0.01; 

    $groups = generateGroups($players); //가능한 팀조합 계산
    $modifiedGroups = modifyGroups($players, $groups); //티어값으로 변환
    $calGroups = calculateGroups($players, $modifiedGroups); //티어총합 계산
    $resultGroups = makeGroups($players, $calGroups, $groups); //4차원배열생성
    
    list($red_team, $blue_team) = convertRatioToPercent($players, $ratio, $resultGroups);
}
?>
<link rel="stylesheet" type="text/css" href="/css/match.css">
<div class="ctitle">
    <div class="pic left" style="background-image:url('/img/lol.png')"></div>
    LOL 내전표 생성 페이지
    <div class="pic right" style="background-image:url('/img/lol.png')"></div>
</div>
<form method="POST" action=""> <!--현재페이지로 폼을 전달-->
    <div class="say">인원수 (2~10):</div>
    <select name="player_count" id="player_count">
        <?php
        // 이전에 선택된 인원수를 유지
        for ($i = 2; $i <= 10; $i++) {
            $selected = ($i == $player_count) ? 'selected' : '';
            echo "<option value='$i' $selected>$i</option>";
        }
        ?>
    </select>
    <br><br>
    <div id="player_details" class="player_details">
        <!-- 처음에는 안보이고 입력하고나면 양식을 유지하는 기능 -->
        <?php
        for ($i = 1; $i <= count($players); $i++) {
            $id_value = isset($players[$i - 1]['id']) ? $players[$i - 1]['id'] : '';
            $tier_value = isset($players[$i - 1]['tier']) ? $players[$i - 1]['tier'] : '';
            echo "
            <div class='info'>
            <label for='player_${i}_id'>ID: </label>
            <input type='text' name='player_${i}_id' id='player_${i}_id' value='$id_value' required>
            <label for='player_${i}_tier'>Tier: </label>
            <select name='player_${i}_tier' id='player_${i}_tier' required>
            <option value='1'>Iron 4</option>
            <option value='2'>Iron 3</option>
            <option value='3'>Iron 2</option>
            <option value='4'>Iron 1</option>
            <option value='5'>Bronze 4</option>
            <option value='6'>Bronze 3</option>
            <option value='7'>Bronze 2</option>
            <option value='8'>Bronze 1</option>
            <option value='9'>Silver 4</option>
            <option value='10'>Silver 3</option>
            <option value='11'>Silver 2</option>
            <option value='12'>Silver 1</option>
            <option value='13'>Gold 4</option>
            <option value='14'>Gold 3</option>
            <option value='15'>Gold 2</option>
            <option value='16'>Gold 1</option>
            <option value='17'>Platinum 4</option>
            <option value='18'>Platinum 3</option>
            <option value='19'>Platinum 2</option>
            <option value='20'>Platinum 1</option>
            <option value='21'>Emerald 4</option>
            <option value='22'>Emerald 3</option>
            <option value='23'>Emerald 2</option>
            <option value='24'>Emerald 1</option>
            <option value='25'>Diamond 4</option>
            <option value='26'>Diamond 3</option>
            <option value='27'>Diamond 2</option>
            <option value='28'>Diamond 1</option>
            <option value='30'>Master ~</option>
            </select>
            </div>";
        }
        ?> 
    </div>
    <div class="res" style="display:none;">
        <label class="range_info">팀 균형 조절<br>(양팀 티어차이)</label><br>
        <label class="range left">최소</label>
        <!--균형값을 설정하는 부분. 기본값으로 이미 설정한 균형값을 가져옴-->
        <input type="range" min="0.01" max="1.00" step="0.01" value="<?php echo $ratio; ?>" id="teamBalance" name="teamBalance" class="tip" data-tip="다양한 팀조합을 보려면 게이지를 조금씩 옮기면서 내전표를 만들어보세요."/>
        <label class="range right">최대</label><br>
        <button type="submit" name="submit">내전표 만들기</button>
    </div>
</form>
<div class='result'>
    <?php if (!empty($red_team)) : ?>
        <div class="team blue">
            <div class="title">Blue팀</div>
            <?php
            foreach ($blue_team as $player) {
                echo "<div class='player'><div class='left'><div class='name'>{$player['id']}</div></div><div class='right'><div class='tier'>" . getTierName($player['tier']) . "</div></div></div>";
            }
            ?>
        </div>
        <div> VS </div>
    <?php endif; ?>
    <?php if (!empty($blue_team)) : ?>
        <div class="team red">
            <div class="title">Red팀</div>
            <?php
            foreach ($red_team as $player) {
                echo "<div class='player'><div class='left'><div class='name'>{$player['id']}</div></div><div class='right'><div class='tier'>" . getTierName($player['tier']) . "</div></div></div>";
            }
            ?>
        </div>
    <?php endif; ?>
</div>
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bottom.php');
?>
<script>
    // 이전에 선택한 티어를 유지하는 JavaScript 코드
    document.addEventListener("DOMContentLoaded", function() {
        <?php
        for ($i = 1; $i <= $player_count; $i++) {
            $tier_value = isset($players[$i - 1]['tier']) ? $players[$i - 1]['tier'] : '1'; //기본값은 브론즈 4
            echo "document.getElementById('player_${i}_tier').value = '$tier_value';";
        }
        ?>

        // 인원수를 선택했을 때 res를 보여줌
        toggleResVisibility();
    });

    document.getElementById('player_count').addEventListener('change', function () {
        var playerCount = parseInt(this.value);
        var playerDetails = document.getElementById('player_details');
        playerDetails.innerHTML = '';
        for (var i = 1; i <= playerCount; i++) {
            playerDetails.innerHTML += `
            <div class='info'>
            <label for="player_${i}_id">ID: </label>
            <input type="text" name="player_${i}_id" id="player_${i}_id" required>
            <label for="player_${i}_tier">Tier: </label>
            <select name="player_${i}_tier" id="player_${i}_tier" required>
            <option value='1'>Iron 4</option>
            <option value='2'>Iron 3</option>
            <option value='3'>Iron 2</option>
            <option value='4'>Iron 1</option>
            <option value='5'>Bronze 4</option>
            <option value='6'>Bronze 3</option>
            <option value='7'>Bronze 2</option>
            <option value='8'>Bronze 1</option>
            <option value='9'>Silver 4</option>
            <option value='10'>Silver 3</option>
            <option value='11'>Silver 2</option>
            <option value='12'>Silver 1</option>
            <option value='13'>Gold 4</option>
            <option value='14'>Gold 3</option>
            <option value='15'>Gold 2</option>
            <option value='16'>Gold 1</option>
            <option value='17'>Platinum 4</option>
            <option value='18'>Platinum 3</option>
            <option value='19'>Platinum 2</option>
            <option value='20'>Platinum 1</option>
            <option value='21'>Emerald 4</option>
            <option value='22'>Emerald 3</option>
            <option value='23'>Emerald 2</option>
            <option value='24'>Emerald 1</option>
            <option value='25'>Diamond 4</option>
            <option value='26'>Diamond 3</option>
            <option value='27'>Diamond 2</option>
            <option value='28'>Diamond 1</option>
            <option value='30'>Master ~</option>
            </select>
            </div>
            `;
        }

        // 인원수를 선택했을 때 res를 보여줌
        toggleResVisibility();
    });

    //res태그를 표시하는 함수
    function toggleResVisibility() {
        var playerCount = parseInt(document.getElementById('player_count').value);
        var res = document.querySelector('.res');
        if (playerCount > 0) {
            res.style.display = 'block';
        } else {
            res.style.display = 'none';
        }
    }
</script>
