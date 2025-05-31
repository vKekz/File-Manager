import { Component, OnInit, signal, WritableSignal } from "@angular/core";
import { IconComponent } from "../icon/icon.component";
import { delay } from "../../../helpers/timeout.helper";
import { ButtonComponent } from "../button/button.component";
import { Router } from "@angular/router";

@Component({
  selector: "app-home",
  imports: [IconComponent, ButtonComponent],
  templateUrl: "./home.component.html",
  styleUrl: "./home.component.css",
})
export class HomeComponent implements OnInit {
  protected readonly iconSize: number = 128;
  protected readonly brandName: WritableSignal<string> = signal("S");

  constructor(private readonly router: Router) {}

  async ngOnInit() {
    await this.animateBrandName();
  }

  protected async goToLogin() {
    await this.router.navigateByUrl("/login");
  }

  private async animateBrandName() {
    const finalTitle = "Shelfy";
    const length = finalTitle.length;

    for (let i = 1; i < length; i++) {
      await delay(175);
      this.brandName.update((value) => value + finalTitle[i]);
    }
  }
}
