import { Component, OnInit, signal, WritableSignal } from "@angular/core";
import { IconComponent } from "../icon/icon.component";
import { delay } from "../../../helpers/timeout.helper";

@Component({
  selector: "app-home",
  imports: [IconComponent],
  templateUrl: "./home.component.html",
  styleUrl: "./home.component.css",
})
export class HomeComponent implements OnInit {
  protected readonly iconSize: number = 128;
  protected readonly title: WritableSignal<string> = signal("");

  async ngOnInit() {
    await this.startAnimation();
  }

  private async startAnimation() {
    const finalTitle = "File Manager";
    const length = finalTitle.length;

    for (let i = 0; i < length; i++) {
      this.title.update((value) => value + finalTitle[i]);
      await delay(150);
    }
  }
}
