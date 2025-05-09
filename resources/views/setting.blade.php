<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>設定ページ</title>
    <link rel="stylesheet" href="{{ asset('css/setting.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @include('partials.head')
    <script src="{{ asset('js/setting.js') }}"></script>
    
</head>
<body>
    <main>
        <div class="header">
            <div class="line-top">
                <img src="{{ asset('/storage/images/top-line.jpg') }}" alt="上ライン">
            
                <!-- 画像の上に重ねる -->
                <div class="header-overlay">
                    <div class="header-container">
                        <div class="app-name">ストログ</div>
                    </div>
                </div>
            </div>

        </div>
        <div class="main">
            <div class="setting-wrapper">
                <div class="setting-title">設定</div>
        
                <div class="setting-item" onclick="window.location.href='/account'">
                    <i class="fa-solid fa-user" style="color:#ff66cc;"></i>
                    <div class="setting-label">アカウント設定</div>
                </div>
        
                <div class="setting-item" onclick="openOverlay('invitation-overlay')">
                    <i class="fa-solid fa-envelope" style="color:#5ce0f0;"></i>
                    <div class="setting-label">招待一覧</div>
                </div>
        
                <div class="setting-item" onclick="openOverlay('policy-overlay')">
                    <i class="fa-solid fa-scroll" style="color:#ff66cc;"></i>
                    <div class="setting-label">ストログ規約</div>
                </div>
        
                <div class="setting-item" onclick="window.location.href='/contact'">
                    <i class="fa-solid fa-comments" style="color:#5ce0f0;"></i>
                    <div class="setting-label">お問い合せ</div>
                </div>
        
                <div class="setting-item" onclick="openOverlay('withdraw-modal')">
                    <i class="fa-solid fa-hand" style="color:#ff66cc;"></i>
                    <div class="setting-label">退会する</div>
                </div>
                

                @if(Auth::user() && Auth::user()->is_admin)
                    <div class="setting-item" onclick="window.location.href='/admin'">
                        <i class="fa-solid fa-user-shield" style="color:#5ce0f0;"></i>
                        <div class="setting-label">管理者ページ</div>
                    </div>
                @endif
        
                
            </div>
        
            <!-- オーバーレイ（フルスクリーンページ風） -->
            <div id="invitation-overlay" class="overlay" style="display: none;">
                
                <!-- 上ライン -->
                <div class="line-top">
                    <img src="{{ asset('/storage/images/top-line.jpg') }}" alt="上ライン">
                    <div class="header-overlay">
                        <div class="header-container">
                            <a href="#" class="back-link" onclick="closeOverlay('invitation-overlay')">← 戻る</a>
                            <div class="app-name">ストログ</div>
                        </div>
                    </div>
                </div>


                <!-- メインエリア -->
                <div class="main">
                    <div class="content">
                        
                        <div class="history-title">保留中招待一覧</div>

                        <div class="invitation-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>

                       

                        @if($pendingInvitations->isEmpty())
                            <p style="text-align: center; margin-top: 50px;">保留中のグループ招待はありません。</p>
                        @else
                            <div class="invitation-list">
                                @foreach($pendingInvitations as $invitation)
                                    <div class="invitation-card">
                                        <div class="invitation-text">
                                            {{ $invitation->group->name }}
                                        </div>
                                        <div class="invitation-buttons">
                                            <form action="{{ route('invitation.respond') }}" method="POST" style="display: flex; gap: 10px;">
                                                @csrf
                                                <input type="hidden" name="invitation_id" value="{{ $invitation->id }}">
                                                @if($totalSpaceCount < 3)
                                                    <button class="accept-btn" name="response" value="accept">参加</button>
                                                @endif
                                                <button class="decline-btn" name="response" value="decline">辞退</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 下ライン -->
                <div class="line-bottom-fixed">
                    <img src="{{ asset('/storage/images/bottom-line.jpg') }}" alt="下ライン">
                </div>

                
            </div>

            <div id="policy-overlay" class="overlay" style="display: none;">
                <div class="header">
                    <div class="line-top">
                        <img src="{{ asset('/storage/images/top-line.jpg') }}" alt="上ライン">
                        <div class="header-overlay">
                            <div class="header-container">
                                <a href="#" class="back-link" onclick="closeOverlay('policy-overlay')">← 戻る</a>
                                <div class="app-name">ストログ</div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="main">
                    <div class="content">
                        <div class="history-title" style="margin-top: 20px;">ストログ規約</div>
            
                        <ul class="policy-menu">
                            <li class="version-item">
                                バージョン
                                <span class="version-number">1.0.0</span>
                            </li>
                            <li onclick="openOverlay('terms-modal')">利用規約</li>
                            <li onclick="openOverlay('privacy-modal')">プライバシーポリシー</li>
                            <li onclick="openOverlay('copyright-modal')">著作権情報</li>
                            <li id="clear-cache-btn" onclick="clearAppCache()">キャッシュクリア</li>

                        </ul>
                    </div>
                </div>

                <!-- 利用規約モーダル -->
                <div id="terms-modal" class="overlay fullscreen-modal">
                    <div class="overlay-content">
                        <button class="close" onclick="closeOverlay('terms-modal')">×</button>
                        <h3>利用規約</h3>
                        <div class="modal-scroll-content">
                                <p>
                                    この利用規約（以下，「本規約」といいます。）は，ストログ（以下，「当社」といいます。）がこのウェブサイト上で提供するサービス（以下，「本サービス」といいます。）の利用条件を定めるものです。登録ユーザーの皆さま（以下，「ユーザー」といいます。）には，本規約に従って，本サービスをご利用いただきます。
                                </p>

                                <h5>第1条（適用）</h5>
                                <p>
                                    本規約は，ユーザーと当社との間の本サービスの利用に関わる一切の関係に適用されるものとします。<br>
                                    当社は本サービスに関し，本規約のほか，ご利用にあたってのルール等，各種の定め（以下，「個別規定」といいます。）をすることがあります。これら個別規定はその名称のいかんに関わらず，本規約の一部を構成するものとします。<br>
                                    本規約の規定が前条の個別規定の規定と矛盾する場合には，個別規定において特段の定めなき限り，個別規定の規定が優先されるものとします。
                                </p>

                                <h5>第2条（利用登録）</h5>
                                <p>
                                    本サービスにおいては，登録希望者が本規約に同意の上，当社の定める方法によって利用登録を申請し，当社がこれを承認することによって，利用登録が完了するものとします。<br>
                                    当社は，利用登録の申請者に以下の事由があると判断した場合，利用登録の申請を承認しないことがあり，その理由については一切の開示義務を負わないものとします。<br>
                                    - 利用登録の申請に際して虚偽の事項を届け出た場合<br>
                                    - 本規約に違反したことがある者からの申請である場合<br>
                                    - その他，当社が利用登録を相当でないと判断した場合
                                </p>

                                <h5>第3条（ユーザーIDおよびパスワードの管理）</h5>
                                <p>
                                    ユーザーは，自己の責任において，本サービスのユーザーIDおよびパスワードを適切に管理するものとします。<br>
                                    ユーザーは，いかなる場合にも，ユーザーIDおよびパスワードを第三者に譲渡または貸与し，もしくは第三者と共用することはできません。当社は，ユーザーIDとパスワードの組み合わせが登録情報と一致してログインされた場合には，そのユーザーIDを登録しているユーザー自身による利用とみなします。<br>
                                    ユーザーID及びパスワードが第三者によって使用されたことによって生じた損害は，当社に故意又は重大な過失がある場合を除き，当社は一切の責任を負わないものとします。
                                </p>

                                <h5>第4条（利用料金および支払方法）</h5>
                                <p>
                                    ユーザーは，本サービスの有料部分の対価として，当社が別途定め，本ウェブサイトに表示する利用料金を，当社が指定する方法により支払うものとします。<br>
                                    ユーザーが利用料金の支払を遅滞した場合には，ユーザーは年14．6％の割合による遅延損害金を支払うものとします。
                                </p>

                                <h5>第5条（禁止事項）</h5>
                                <p>
                                    ユーザーは，本サービスの利用にあたり，以下の行為をしてはなりません。<br>
                                    - 法令または公序良俗に違反する行為<br>
                                    - 犯罪行為に関連する行為<br>
                                    - 本サービスの内容等，本サービスに含まれる著作権，商標権ほか知的財産権を侵害する行為<br>
                                    - 当社，ほかのユーザー，またはその他第三者のサーバーまたはネットワークの機能を破壊したり，妨害したりする行為<br>
                                    - 本サービスによって得られた情報を商業的に利用する行為<br>
                                    - 当社のサービスの運営を妨害するおそれのある行為<br>
                                    - 不正アクセスをし，またはこれを試みる行為<br>
                                    - 他のユーザーに関する個人情報等を収集または蓄積する行為<br>
                                    - 不正な目的を持って本サービスを利用する行為<br>
                                    - 本サービスの他のユーザーまたはその他の第三者に不利益，損害，不快感を与える行為<br>
                                    - 他のユーザーに成りすます行為<br>
                                    - 当社が許諾しない本サービス上での宣伝，広告，勧誘，または営業行為<br>
                                    - 面識のない異性との出会いを目的とした行為<br>
                                    - 当社のサービスに関連して，反社会的勢力に対して直接または間接に利益を供与する行為<br>
                                    - その他，当社が不適切と判断する行為
                                </p>

                                <h5>第6条（本サービスの提供の停止等）</h5>
                                <p>
                                    当社は，以下のいずれかの事由があると判断した場合，ユーザーに事前に通知することなく本サービスの全部または一部の提供を停止または中断することができるものとします。<br>
                                    - 本サービスにかかるコンピュータシステムの保守点検または更新を行う場合<br>
                                    - 地震，落雷，火災，停電または天災などの不可抗力により，本サービスの提供が困難となった場合<br>
                                    - コンピュータまたは通信回線等が事故により停止した場合<br>
                                    - その他，当社が本サービスの提供が困難と判断した場合<br>
                                    当社は，本サービスの提供の停止または中断により，ユーザーまたは第三者が被ったいかなる不利益または損害についても，一切の責任を負わないものとします。
                                </p>

                                <h5>第7条（利用制限および登録抹消）</h5>
                                <p>
                                    当社は，ユーザーが以下のいずれかに該当する場合には，事前の通知なく，ユーザーに対して，本サービスの全部もしくは一部の利用を制限し，またはユーザーとしての登録を抹消することができるものとします。<br>
                                    - 本規約のいずれかの条項に違反した場合<br>
                                    - 登録事項に虚偽の事実があることが判明した場合<br>
                                    - 料金等の支払債務の不履行があった場合<br>
                                    - 当社からの連絡に対し，一定期間返答がない場合<br>
                                    - 本サービスについて，最終の利用から一定期間利用がない場合<br>
                                    - その他，当社が本サービスの利用を適当でないと判断した場合<br>
                                    当社は，本条に基づき当社が行った行為によりユーザーに生じた損害について，一切の責任を負いません。
                                </p>

                                <h5>第8条（退会）</h5>
                                <p>
                                    ユーザーは，当社の定める退会手続により，本サービスから退会できるものとします。
                                </p>

                                <h5>第9条（保証の否認および免責事項）</h5>
                                <p>
                                    当社は，本サービスに事実上または法律上の瑕疵（安全性，信頼性，正確性，完全性，有効性，特定の目的への適合性，セキュリティなどに関する欠陥，エラーやバグ，権利侵害などを含みます。）がないことを明示的にも黙示的にも保証しておりません。<br>
                                    当社は，本サービスに起因してユーザーに生じたあらゆる損害について、当社の故意又は重過失による場合を除き、一切の責任を負いません。ただし，本サービスに関する当社とユーザーとの間の契約（本規約を含みます。）が消費者契約法に定める消費者契約となる場合，この免責規定は適用されません。<br>
                                    前項ただし書に定める場合であっても，当社は，当社の過失（重過失を除きます。）による債務不履行または不法行為によりユーザーに生じた損害のうち特別な事情から生じた損害（当社またはユーザーが損害発生につき予見し，または予見し得た場合を含みます。）について一切の責任を負いません。また，当社の過失（重過失を除きます。）による債務不履行または不法行為によりユーザーに生じた損害の賠償は，ユーザーから当該損害が発生した月に受領した利用料の額を上限とします。<br>
                                    当社は，本サービスに関して，ユーザーと他のユーザーまたは第三者との間において生じた取引，連絡または紛争等について一切責任を負いません。
                                </p>

                                <h5>第10条（サービス内容の変更等）</h5>
                                <p>
                                    当社は，ユーザーへの事前の告知をもって、本サービスの内容を変更、追加または廃止することがあり、ユーザーはこれを承諾するものとします。
                                </p>

                                <h5>第11条（利用規約の変更）</h5>
                                <p>
                                    当社は以下の場合には、ユーザーの個別の同意を要せず、本規約を変更することができるものとします。<br>
                                    - 本規約の変更がユーザーの一般の利益に適合するとき。<br>
                                    - 本規約の変更が本サービス利用契約の目的に反せず、かつ、変更の必要性、変更後の内容の相当性その他の変更に係る事情に照らして合理的なものであるとき。<br>
                                    当社はユーザーに対し、前項による本規約の変更にあたり、事前に、本規約を変更する旨及び変更後の本規約の内容並びにその効力発生時期を通知します。
                                </p>

                                <h5>第12条（個人情報の取扱い）</h5>
                                <p>
                                    当社は，本サービスの利用によって取得する個人情報については，当社「プライバシーポリシー」に従い適切に取り扱うものとします。
                                </p>

                                <h5>第13条（通知または連絡）</h5>
                                <p>
                                    ユーザーと当社との間の通知または連絡は，当社の定める方法によって行うものとします。当社は,ユーザーから,当社が別途定める方式に従った変更届け出がない限り,現在登録されている連絡先が有効なものとみなして当該連絡先へ通知または連絡を行い,これらは,発信時にユーザーへ到達したものとみなします。
                                </p>

                                <h5>第14条（権利義務の譲渡の禁止）</h5>
                                <p>
                                    ユーザーは，当社の書面による事前の承諾なく，利用契約上の地位または本規約に基づく権利もしくは義務を第三者に譲渡し，または担保に供することはできません。
                                </p>

                                <h5>第15条（準拠法・裁判管轄）</h5>
                                <p>
                                    本規約の解釈にあたっては，日本法を準拠法とします。<br>
                                    本サービスに関して紛争が生じた場合には，当社の本店所在地を管轄する裁判所を専属的合意管轄とします。
                                </p>
                            <p>以上</p>
                        </div>
                    </div>
                </div>

                <!-- プライバシーポリシーモーダル -->
                <div id="privacy-modal" class="overlay fullscreen-modal">
                    <div class="overlay-content">
                        <button class="close" onclick="closeOverlay('privacy-modal')">×</button>
                        <h3>プライバシーポリシー</h3>
                        <div class="modal-scroll-content">
                            <p>
                                ストログ（以下，「当社」といいます。）は，本ウェブサイト上で提供するサービス（以下,「本サービス」といいます。）における，ユーザーの個人情報の取扱いについて，以下のとおりプライバシーポリシー（以下，「本ポリシー」といいます。）を定めます。
                            </p>
                            <h5>第1条（個人情報）</h5>
                            <p>
                                「個人情報」とは，個人情報保護法にいう「個人情報」を指すものとし，生存する個人に関する情報であって，当該情報に含まれる氏名，生年月日，住所，電話番号，連絡先その他の記述等により特定の個人を識別できる情報及び容貌，指紋，声紋にかかるデータ，及び健康保険証の保険者番号などの当該情報単体から特定の個人を識別できる情報（個人識別情報）を指します。
                            </p>
                
                            <h5>第2条（個人情報の収集方法）</h5>
                            <p>
                                当社は，ユーザーが利用登録をする際に氏名，生年月日，住所，電話番号，メールアドレス，銀行口座番号，クレジットカード番号，運転免許証番号などの個人情報をお尋ねすることがあります。また，ユーザーと提携先などとの間でなされたユーザーの個人情報を含む取引記録や決済に関する情報を,当社の提携先（情報提供元，広告主，広告配信先などを含みます。以下，｢提携先｣といいます。）などから収集することがあります。
                            </p>
                
                            <h5>第3条（個人情報を収集・利用する目的）</h5>
                            <p>
                                当社が個人情報を収集・利用する目的は，以下のとおりです。<br>
                                - 当社サービスの提供・運営のため<br>
                                - ユーザーからのお問い合わせに回答するため（本人確認を行うことを含む）<br>
                                - ユーザーが利用中のサービスの新機能，更新情報，キャンペーン等及び当社が提供する他のサービスの案内のメールを送付するため<br>
                                - メンテナンス，重要なお知らせなど必要に応じたご連絡のため<br>
                                - 利用規約に違反したユーザーや，不正・不当な目的でサービスを利用しようとするユーザーの特定をし，ご利用をお断りするため<br>
                                - ユーザーにご自身の登録情報の閲覧や変更，削除，ご利用状況の閲覧を行っていただくため<br>
                                - 有料サービスにおいて，ユーザーに利用料金を請求するため<br>
                                - 上記の利用目的に付随する目的
                            </p>
                
                            <h5>第4条（利用目的の変更）</h5>
                            <p>
                                当社は，利用目的が変更前と関連性を有すると合理的に認められる場合に限り，個人情報の利用目的を変更するものとします。<br>
                                利用目的の変更を行った場合には，変更後の目的について，当社所定の方法により，ユーザーに通知し，または本ウェブサイト上に公表するものとします。
                            </p>
                
                            <h5>第5条（個人情報の第三者提供）</h5>
                            <p>
                                当社は，次に掲げる場合を除いて，あらかじめユーザーの同意を得ることなく，第三者に個人情報を提供することはありません。ただし，個人情報保護法その他の法令で認められる場合を除きます。<br>
                                - 人の生命，身体または財産の保護のために必要がある場合であって，本人の同意を得ることが困難であるとき<br>
                                - 公衆衛生の向上または児童の健全な育成の推進のために特に必要がある場合であって，本人の同意を得ることが困難であるとき<br>
                                - 国の機関もしくは地方公共団体またはその委託を受けた者が法令の定める事務を遂行することに対して協力する必要がある場合であって，本人の同意を得ることにより当該事務の遂行に支障を及ぼすおそれがあるとき<br>
                                - 予め次の事項を告知あるいは公表し，かつ当社が個人情報保護委員会に届出をしたとき<br>
                                -- 利用目的に第三者への提供を含むこと<br>
                                -- 第三者に提供されるデータの項目<br>
                                -- 第三者への提供の手段または方法<br>
                                -- 本人の求めに応じて個人情報の第三者への提供を停止すること<br>
                                -- 本人の求めを受け付ける方法
                            </p>
                
                            <h5>第6条（個人情報の開示）</h5>
                            <p>
                                当社は，本人から個人情報の開示を求められたときは，本人に対し，遅滞なくこれを開示します。ただし，開示することにより次のいずれかに該当する場合は，その全部または一部を開示しないこともあり，開示しない決定をした場合には，その旨を遅滞なく通知します。なお，個人情報の開示に際しては，1件あたり1，000円の手数料を申し受けます。<br>
                                - 本人または第三者の生命，身体，財産その他の権利利益を害するおそれがある場合<br>
                                - 当社の業務の適正な実施に著しい支障を及ぼすおそれがある場合<br>
                                - その他法令に違反することとなる場合
                            </p>
                
                            <h5>第7条（個人情報の訂正および削除）</h5>
                            <p>
                                ユーザーは，当社の保有する自己の個人情報が誤った情報である場合には，当社が定める手続きにより，当社に対して個人情報の訂正，追加または削除（以下，「訂正等」といいます。）を請求することができます。<br>
                                当社は，ユーザーから前項の請求を受けてその請求に応じる必要があると判断した場合には，遅滞なく，当該個人情報の訂正等を行うものとします。<br>
                                当社は，前項の規定に基づき訂正等を行った場合，または訂正等を行わない旨の決定をしたときは遅滞なく，これをユーザーに通知します。
                            </p>
                
                            <h5>第8条（個人情報の利用停止等）</h5>
                            <p>
                                当社は，本人から，個人情報が，利用目的の範囲を超えて取り扱われているという理由，または不正の手段により取得されたものであるという理由により，その利用の停止または消去（以下，「利用停止等」といいます。）を求められた場合には，遅滞なく必要な調査を行います。<br>
                                前項の調査結果に基づき，その請求に応じる必要があると判断した場合には，遅滞なく，当該個人情報の利用停止等を行います。<br>
                                当社は，前項の規定に基づき利用停止等を行った場合，または利用停止等を行わない旨の決定をしたときは，遅滞なく，これをユーザーに通知します。<br>
                                前2項にかかわらず，利用停止等に多額の費用を有する場合その他利用停止等を行うことが困難な場合であって，ユーザーの権利利益を保護するために必要なこれに代わるべき措置をとれる場合は，この代替策を講じるものとします。
                            </p>
                
                            <h5>第9条（プライバシーポリシーの変更）</h5>
                            <p>
                                本ポリシーの内容は，法令その他本ポリシーに別段の定めのある事項を除いて，ユーザーに通知することなく，変更することができるものとします。<br>
                                当社が別途定める場合を除いて，変更後のプライバシーポリシーは，本ウェブサイトに掲載したときから効力を生じるものとします。
                            </p>
                
                            <h5>第10条（お問い合わせ窓口）</h5>
                            <p>
                                本ポリシーに関するお問い合わせは，<a href="mailto:timemafia.240803@gmail.com?subject=お問い合わせ&body=ここにメッセージを入力してください">こちら</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 著作権情報モーダル -->
                <div id="copyright-modal" class="overlay fullscreen-modal">
                    <div class="overlay-content">
                        <button class="close" onclick="closeOverlay('copyright-modal')">×</button>
                        <h3>著作権情報</h3>
                        <div class="modal-scroll-content">
                            <p>
                                本ウェブサイトおよび本サービス（以下、「本サービス」といいます。）に掲載されているすべてのコンテンツ（以下、「コンテンツ」といいます。）に関する著作権情報を以下に定めます。ユーザーの皆様は、以下の内容を遵守の上、適切に本サービスをご利用ください。
                            </p>
                    
                            <h5>第1条（著作権の帰属）</h5>
                            <p>
                                本サービスに掲載されているテキスト、画像、音声、動画、プログラム、デザインなど、すべてのコンテンツの著作権は、当社または当社に使用を許諾した権利者（以下「権利者」といいます。）に帰属します。無断での使用、複製、改変、配布、販売、公開、展示、商業的利用などの行為は、著作権法およびその他の法令により禁止されています。
                            </p>
                    
                            <h5>第2条（コンテンツの使用制限）</h5>
                            <p>
                                ユーザーは、以下の行為を行ってはなりません。<br>
                                - 本サービスに掲載されているコンテンツを、個人的な利用の範囲を超えて、権利者の事前の許可なく使用すること。<br>
                                - 本サービスのコンテンツを無断で複製、改変、翻訳、転載、送信、配布、販売、公開、展示すること。<br>
                                - コンテンツを商業目的で使用すること、または営利目的で第三者に提供すること。<br>
                                - 本サービスのコンテンツを、他のウェブサイトやメディア、出版物に無断で転載すること。
                            </p>
                    
                            <h5>第3条（引用およびリンクについて）</h5>
                            <p>
                                1. 本サービス内のコンテンツを引用する場合は、以下の条件を満たすものとします。<br>
                                - 引用箇所が「本サービス」の内容であることを明示すること（例：引用元として「本サービス名」を記載）。<br>
                                - 引用が私的利用や教育目的の範囲内であり、営利目的ではないこと。<br>
                                2. 本サービスへのリンクは、営利目的でなく、かつ当社の意図に反しない場合に限り設定することができます。ただし、リンクを設定する際は、事前に当社までご連絡ください。
                            </p>
                    
                            <h5>第4条（第三者の著作物に関する取り扱い）</h5>
                            <p>
                                本サービス内で使用される一部のコンテンツは、第三者の著作権を含むものがあります。これらのコンテンツに関する権利は、各権利者に帰属し、当社の許可を得た範囲内で使用しています。第三者の著作物の利用に関する詳細については、各権利者の定めに従ってください。
                            </p>
                    
                            <h5>第5条（著作権侵害について）</h5>
                            <p>
                                ユーザーが本サービスのコンテンツを無断で使用し、当社または第三者の著作権を侵害した場合、当社は速やかにその侵害行為の停止を求めるとともに、法的措置（損害賠償請求、訴訟等）を含む適切な対応を行います。また、著作権侵害により生じた損害について、ユーザーは全額賠償する責任を負うものとします。
                            </p>
                    
                            <h5>第6条（免責事項）</h5>
                            <p>
                                本サービスに掲載されるコンテンツは、正確性および最新性を維持するよう努めていますが、その完全性や正確性を保証するものではありません。ユーザーが本サービスのコンテンツを利用したことにより生じたいかなる損害についても、当社は一切の責任を負いません。
                            </p>
                    
                            <h5>第7条（本著作権情報の変更について）</h5>
                            <p>
                                当社は、必要に応じて本著作権情報の内容を変更することがあります。変更後の内容は、本ウェブサイトに掲載した時点から適用されるものとします。ユーザーは定期的に本ページを確認し、最新の著作権情報を把握するようにしてください。
                            </p>
                    
                            <h5>第8条（お問い合わせ）</h5>
                            <p>
                                本サービスおよびコンテンツに関する著作権や権利に関するお問い合わせは、<a href="mailto:timemafia.240803@gmail.com?subject=お問い合わせ&body=ここにメッセージを入力してください">こちら</a><br>
                            </p>
                        </div>
                    </div>
                </div>
            
                <div class="line-bottom-fixed">
                    <img src="{{ asset('/storage/images/bottom-line.jpg') }}" alt="下ライン">
                </div>
            </div>
            
            
        
            <!-- オーバーレイ：退会確認 -->
            <div id="withdraw-modal" class="overlay fullscreen-modal" style="display: none;">
                <div class="overlay-content">
                    <button class="close" onclick="closeOverlay('withdraw-modal')">&times;</button>
                    <h3>退会確認</h3>
                    <div class="modal-scroll-content">
                        <p>本当に退会しますか？<br>すべてのデータが削除されます。</p>
                    </div>
                    <div class="confirm-buttons">
                        <form method="POST" action="{{ route('withdraw') }}">
                            @csrf
                            <button type="submit" class="confirm">退会する</button>
                            <button type="button" class="cancel" onclick="closeOverlay('withdraw-modal')">キャンセル</button>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- フッター背景画像（画面最下部に固定） -->
        <div class="line-bottom-fixed">
            <img src="{{ asset('/storage/images/bottom-line.jpg') }}" alt="下ライン">
        </div>

        <!-- フッターのボタン群（画像の上に表示） -->
        <div class="footer-overlay-fixed">
            
            <button onclick="window.location.href='/top'">
                <i class="fa-solid fa-house"></i><br>ホーム
            </button>
            <button onclick="window.location.href='/history'">
                <i class="fa-solid fa-clock"></i><br>履歴
            </button>
            <button onclick="window.location.href='/settings'">
                <i class="fa-solid fa-gear"></i><br>設定
            </button>
        </div>
    </main>
    

    <script>
        
        function openOverlay(id) {
            // まず全部のモーダルを閉じる
            document.querySelectorAll('.fullscreen-modal').forEach(modal => {
                modal.style.display = 'none';
            });

            // 指定されたオーバーレイだけ開く
            document.getElementById(id).style.display = 'flex';
        }

        function closeOverlay(id) {
            const el = document.getElementById(id);
            if (el) {
                el.style.display = 'none';
            }
        }

        function clearAppCache() {
            const clearButton = document.getElementById('clear-cache-btn');
            
            if (!clearButton) return; // ボタンなかったら何もしない

            // 押したらすぐに無効化（連打防止）
            clearButton.onclick = null;

            if ('caches' in window) {
                caches.keys().then(function(cacheNames) {
                    return Promise.all(
                        cacheNames.map(function(cacheName) {
                            return caches.delete(cacheName);
                        })
                    );
                }).then(function() {
                    console.log('キャッシュクリア完了');

                    // ボタンの表示を変更！
                    clearButton.textContent = 'キャッシュクリア完了しました';
                    clearButton.style.pointerEvents = 'none'; // クリック無効にしておく（オプション）
                    clearButton.style.color = '#999'; // 少し色も薄くして「完了」感を出す（オプション）

                }).catch(function(error) {
                    console.error('キャッシュクリア失敗:', error);
                });
            } else {
                console.error('キャッシュAPIがサポートされていません');
            }
        }


    </script>
</body>
</html>
